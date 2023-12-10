#!/usr/bin/env bash

set -e

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

docker build --build-arg "PHP_VERSION=8.2" -t phespro-react .
docker run -it --rm -p 8080:80 -v "$DIR:/code" -w "/code" phespro-react "$@"