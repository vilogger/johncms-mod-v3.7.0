/**
  * Code auto load by Izero
  */

var request = {
    method: 'POST',
    action: '/auto/auto.php',
    paramaters: 'load=true'
};

var notification = 'notification'; // ID tag html cho thông báo
var message = 'message'; // ID tag html cho tin nhắn
var friend = 'friend'; // ID tag html cho kết bạn
var friend_href = 'friend_href'; // ID tag html để lấy link tới thông báo mới
var friend_link = null;
var group = 'group'; // ID tag html cho hội nhóm
var group_href = 'group_href'; // ID tag html để lấy link tới thông báo mới
var group_link = null;
var message_href = 'message_href'; // ID tag html để lấy link tới tin nhắn mới
var message_link = null;
var time = 5; // Thời gian cập nhật tính theo giây
var http = null; // XMLHTTPRequest

function xmlhttp()
{
    if (window.XMLHttpRequest)
        return new XMLHttpRequest();
    else
        return new ActiveXObject('Microsoft.XMLHTTP');
}

function statusInvalidate(array)
{
    if (notification != null && array.notification != 'undefined') {
        notification.innerHTML = array.notification;

        if (array.notification <= 0)
            notification.style.display = 'none';
        else
            notification.style.display = '';
    }

    if (message != null && array.message != 'undefined') {
        message.innerHTML = array.message;

        if (array.message <= 0) {
            message.style.display = 'none';

            if (message_href != null)
                message_href.href = message_link;
        } else {
            message.style.display = '';

            if (message_href != null)
                message_href.href = message_link + '?act=new';
        }
    }

    if (friend != null && array.friend != 'undefined') {
        friend.innerHTML = array.friend;

        if (array.friend <= 0) {
            friend.style.display = 'none';

            if (friend_href != null)
                friend_href.href = friend_link;
        } else {
            friend.style.display = '';

            if (friend_href != null)
                friend_href.href = friend_link + '&do=offers';
        }
    }

    if (group != null && array.group != 'undefined') {
        group.innerHTML = array.group;

        if (array.group <= 0) {
            group.style.display = 'none';

            if (group_href != null)
                group_href.href = group_link;
        } else {
            group.style.display = '';

            if (group_href != null)
                group_href.href = group_link + '?act=top&mod=my_new_comm';
        }
    }
}

window.onload = function()
{
    notification = document.getElementById(notification);
    message = document.getElementById(message);
    message_href = document.getElementById(message_href);
    friend = document.getElementById(friend);
    friend_href = document.getElementById(friend_href);
    group = document.getElementById(group);
    group_href = document.getElementById(group_href);
    http = xmlhttp();

    if (message_href != null)
        message_link = message_href.href;

    if (friend_href != null)
        friend_link = friend_href.href;

    if (group_href != null)
        group_link = group_href.href;

    /* Initializing */
    {
        var array = {
            notification: 0,
            message: 0,
            friend: 0,
            group: 0
        };

        if (notification != null)
            array.notification = parseInt(notification.innerHTML);

        if (message != null)
            array.message = parseInt(message.innerHTML);

        if (friend != null)
            array.friend = parseInt(friend.innerHTML);

        if (group != null)
            array.group = parseInt(group.innerHTML);

        statusInvalidate(array);
    }

    http.onreadystatechange = function(event) {
        if (http.readyState == 4 && http.status == 200) {
            try {
                statusInvalidate(JSON.parse(http.responseText));
            } catch (e) {

            }
        }
    };

    setInterval(function()
    {
        http.open(request.method, request.action, true);
        http.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        http.send(request.paramaters);
    }, time * 1000);

};