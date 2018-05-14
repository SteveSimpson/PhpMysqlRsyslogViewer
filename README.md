# PhpMysqlRsyslogViewer
A simple PHP page to search / view rsyslog stored in MySQL

I'm making this into what I need for my envionment, but it is based on a very elegant and simple script by Andrew Galdes. You check out his original post and his post about rsyslog to MySQL at the following links:

* https://www.agix.com.au/simple-rsyslogmysql-log-viewer-in-php/
* http://www.agix.com.au/rsyslog-and-mysql-on-centos7-and-redhat-7/

## Backstory
I needed a simple way to view /filter/sort rsyslog data. Thought I found a program, it was bloated and didn't fit the default schema that rsyslog is distributing (with CentOS 7). I found the latter both ironic and frustrating since the rsyslog wiki pointed to this program. Maybe I missed something, but Google seemed to confirm my findings. Now I could have figured out what the fields were supposed to look like and created a view, I saw hints of others trying that.

That's when I came across Andrew's script. It was from a couple of years ago, but he did everything I really needed in 124 lines of simple PHP. COOL! - Simple is better. Yeah, it had a couple of things that I wanted to clean up, but all-in-all a very elegant solution.

Anyway, I hope this helps other people.
