[program:crowbar-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/crowbar/artisan queue:work --sleep=3 --tries=3 --daemon
autostart=true
autorestart=true
numprocs=10
redirect_stderr=true
stdout_logfile=/var/www/html/crowbar/worker.log
