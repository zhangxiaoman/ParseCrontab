# ParseCrontab
parse linux crontab &amp; support sec

## eg

**linux crontab format**
```
¢ php test.php "8 * * * *" 0
'2017-11-14 16:08:45'
array (
  1 => 1,
)%
```

**support second**
```
¢ php test.php "*/15 9 * * * *" 0
'2017-11-14 16:09:12'
array (
  0 => 0,
  15 => 15,
  30 => 30,
  45 => 45,
)%

```
