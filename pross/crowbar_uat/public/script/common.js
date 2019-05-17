
function writeCookie(name,value,days) {
    var date, expires;
    if (days) {
        date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        expires = "; expires=" + date.toGMTString();
    }else{
        expires = "";
    }
    document.cookie = name + "=" + value + expires + "; path=/";
}

function readCookie(name) {
    var i, c, ca, nameEQ = name + "=";
    ca = document.cookie.split(';');
    for(i=0;i < ca.length;i++) {
        c = ca[i];
        while (c.charAt(0)==' ') {
            c = c.substring(1,c.length);
        }
        if (c.indexOf(nameEQ) == 0) {
            return c.substring(nameEQ.length,c.length);
        }
    }
    return '';
}

function sent_desktop_notification(notification_id, title, notification, image_url, redirect_url) {
    if(Notification){
        var notification = new Notification(title, { 
            icon: asset_url+'/images/push-notification-logo.png',
            body: notification,
            tag: notification_id
        });
        
        if(redirect_url){
            notification.onclick = function () {
                /* window.open(redirect_url);*/
                /* MARKING DESKTOP NOTIFICATION AS SENT */
                $.ajax({url: base_url+'/notification/mark/read?notification_id='+notification_id,type: 'get'});
            };
        }

        /* MARKING DESKTOP NOTIFICATION AS SENT */
        $.ajax({url: base_url+'/notification/desktop/mark/read?notification_id='+notification_id,type: 'get'});
    }
}