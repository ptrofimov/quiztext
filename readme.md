# QuizText

QuizText is markdown-inspired text-based human-readable markup language
to write down quiz questions with options and right answers.

It was created as an alternative to XML-based (QuizML)[http://theses.lib.polyu.edu.hk/handle/200/3476].

Syntax supports:
- single-choice questions
- multiple-choice questions
- input questions

### Example

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

What is the name of the most popular Linux console text editor:
(input question: you need to type "vim" to give a right answer)
= vim
```

This repository contains PHP-written parser for QuizText format.
