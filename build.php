<?php

system('php gekosale propel:diff');
system('php gekosale propel:migration');
system('php gekosale propel:build');