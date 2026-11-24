#!/usr/bin/env bash
# Runs one example's e2e.sh against this checkout of invoice-client: examples/ci/recipe.sh create-invoice
# The example, the fake API and these scripts are copied to a temporary directory first,
# so the checkout stays untouched. Needs php, composer and free ports 8701-8706.
set -euo pipefail
examples="$(cd "$(dirname "$0")/.." && pwd)"
name="${1:?usage: $0 <example>}"
[[ -x "$examples/$name/e2e.sh" ]] || { echo "fail: $examples/$name/e2e.sh not found" >&2; exit 1; }
work="$(mktemp -d)"
trap 'rm -rf "$work"' EXIT
cp -R "$examples/$name" "$work/$name"
cp -R "$examples/ci" "$work/ci"
cp -R "$examples/fake-api" "$work/fake-api"
rm -rf "$work/$name/vendor" "$work/$name/composer.lock"
"$examples/ci/use-local-invoice-client.sh" "$work/$name"
"$work/$name/e2e.sh"
# UPDATE_EXPECTED=1 examples/ci/recipe.sh <example> refreshes the expected output in the checkout
if [[ "${UPDATE_EXPECTED:-}" == 1 ]]; then
    cp "$work/$name/expected-output.txt" "$examples/$name/expected-output.txt"
fi
