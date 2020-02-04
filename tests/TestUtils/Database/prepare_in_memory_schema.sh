#!/usr/bin/env sh

cat $1 | sed -e 's/`\s*text/` varchar(2048)/i' | sed -e 's/`\s*mediumtext/` varchar(4096)/i' | sed 's/ENGINE=InnoDB/ENGINE=MEMORY/i' > $2
