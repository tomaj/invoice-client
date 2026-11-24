#!/usr/bin/env bash
# Starts the fake API, runs run.php and compares its output with expected-output.txt.
set -euo pipefail
cd "$(dirname "$0")"
[[ -f vendor/autoload.php ]] || { echo "fail: vendor/ is missing, run composer install first" >&2; exit 1; }
source ../ci/lib.sh
fake_api 8704
expect_output php run.php
