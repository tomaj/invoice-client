#!/usr/bin/env bash
# Runs the PHPUnit tests of the example application and compares the test list with expected-output.txt.
# No fake API: TestTransport and Guzzle's MockHandler need no server.
set -euo pipefail
cd "$(dirname "$0")"
[[ -f vendor/autoload.php ]] || { echo "fail: vendor/ is missing, run composer install first" >&2; exit 1; }
source ../ci/lib.sh

# the PHPUnit version, run time and memory differ between runs: keep the test list and the summary
tests() {
    vendor/bin/phpunit --testdox --colors=never --no-progress | grep -v -e '^PHPUnit ' -e '^Runtime:' -e '^Configuration:' -e '^Time:' -e '^$'
}
expect_output tests
