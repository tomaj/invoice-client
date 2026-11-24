#!/usr/bin/env bash
# Points an example at this checkout of invoice-client:
#   examples/ci/use-local-invoice-client.sh /path/to/example
# Replaces the example's path repository with the absolute path of the repository root, requires
# tomaj/invoice-client:dev-master from it and installs the example. Used by the examples workflow, so a
# pull request runs the examples against its own code. It rewrites composer.json: run it on a copy.
set -euo pipefail
root="$(cd "$(dirname "$0")/../.." && pwd)"
project="${1:?usage: $0 /path/to/example}"
cd "$project"
# shellcheck disable=SC2016 # $argv is PHP, not shell
# json_encode, so a checkout path with quotes or backslashes still gives valid JSON
repository="$(php -r 'echo json_encode(["type" => "path", "url" => $argv[1], "options" => ["symlink" => false, "versions" => ["tomaj/invoice-client" => "dev-master"]]], JSON_UNESCAPED_SLASHES);' "$root")"
composer config repositories.invoice-client "$repository"
composer install --no-interaction --no-progress
# fail when Composer took invoice-client from anywhere else
diff -rq "$root/src" vendor/tomaj/invoice-client/src > /dev/null \
    || { echo "fail: vendor/tomaj/invoice-client is not this checkout" >&2; exit 1; }
echo "tomaj/invoice-client installed from $root"
