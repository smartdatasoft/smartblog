<?php

$htupdate = '# Images Blog

RewriteRule ^blog/([0-9]+)(\-[\.*_a-zA-Z0-9-]*)(-[0-9]+)?/.+\.jpg$ %{ENV:REWRITEBASE}modules/smartblog/images/$1$2$3.jpg [L]
RewriteRule ^blog/([a-zA-Z_-]+)(-[0-9]+)?/.+\.jpg$ %{ENV:REWRITEBASE}modules/smartblog/images/$1$2.jpg [L]';

