# QuizText

QuizText is markdown-inspired text-based format to write quiz questions with options and right answers.

## Example

```
Who famously said "640K ought to be enough for anybody."?
(single-choice question with right answer "BillGates")
+ Bill Gates
- Steve Jobs
- Steve Wozniak
- None of the above

A DNS translates a domain name into what?
(alt syntax: single-choice question with right answer "IP")
( ) Binary
( ) Hex
(x) IP
( ) URL

Which network protocols are used to send and receive e-mail?
(multiple-choice question with right answers "POP3" and "SMTP")
- FTP
- SSH
+ POP3
+ SMTP

Which of the following is not a social network site?
(alt syntax: multiple-choice question with right answers "Amazon" and "Yahoo")
[x] Amazon
[ ] MySpace
[ ] Orkut
[x] Yahoo
```

This repository contains PHP-written parser for QuizText format.
