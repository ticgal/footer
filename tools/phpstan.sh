#!/bin/bash

TOOL_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" >/dev/null 2>&1 && pwd)"
cd "$TOOL_DIR/.."

if [ ! -f "vendor/bin/phpstan" ]; then
    echo "Error: PHPStan not found in vendor/bin/phpstan (run composer install)"
    exit 1
fi

vendor/bin/phpstan analyze --ansi --memory-limit=2G --no-interaction
