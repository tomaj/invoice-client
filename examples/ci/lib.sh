#!/usr/bin/env bash
# Helpers for the e2e.sh scripts. Sourced, not run. Needs php.

# fake_api <port>: starts the fake chcemfakturu.sk API (examples/fake-api/server.php) with an empty
# state on 127.0.0.1:<port>, exports FAKE_API_URL and waits until it accepts connections.
# The server is stopped when the script exits.
fake_api() {
    local port="$1"
    local dir
    dir="$(cd "$(dirname "${BASH_SOURCE[0]}")/../fake-api" && pwd)"
    FAKE_API_STATE="$(mktemp)"
    rm -f "$FAKE_API_STATE"
    FAKE_API_LOG="$(mktemp)"
    export FAKE_API_STATE FAKE_API_URL="http://127.0.0.1:$port"
    if php -r 'exit(@fsockopen("127.0.0.1", (int) $argv[1]) ? 0 : 1);' "$port" 2>/dev/null; then
        echo "fail: port $port is already in use" >&2
        exit 1
    fi
    php -S "127.0.0.1:$port" "$dir/server.php" > "$FAKE_API_LOG" 2>&1 &
    FAKE_API_PID=$!
    disown "$FAKE_API_PID"
    trap 'kill "$FAKE_API_PID" 2>/dev/null || true; rm -f "$FAKE_API_STATE" "$FAKE_API_LOG"' EXIT
    for _ in $(seq 1 50); do
        php -r 'exit(@fsockopen("127.0.0.1", (int) $argv[1]) ? 0 : 1);' "$port" 2>/dev/null && return 0
        sleep 0.1
    done
    echo "fail: the fake API did not start" >&2
    cat "$FAKE_API_LOG" >&2
    exit 1
}

# expect_output <command...>: runs the command and compares its output with expected-output.txt.
# With UPDATE_EXPECTED=1 it writes expected-output.txt instead (review the diff before committing).
expect_output() {
    local actual
    actual="$(mktemp)"
    "$@" > "$actual"
    if [[ "${UPDATE_EXPECTED:-}" == 1 ]]; then
        mv "$actual" expected-output.txt
        echo "updated: expected-output.txt"
        return 0
    fi
    if ! diff -u expected-output.txt "$actual"; then
        echo "fail: the output of $* differs from expected-output.txt" >&2
        rm -f "$actual"
        exit 1
    fi
    rm -f "$actual"
    echo "ok: $* prints expected-output.txt"
}
