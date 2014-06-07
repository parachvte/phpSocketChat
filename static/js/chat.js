$(document).ready(function(){
    //config
    var host = 'http://127.0.0.1';
    //var host = 'http://ryan.com';
    var client = host + '/client/client.php';

    //objects
    var chatViewObj = $('#chat-view'),
        submitBtnObj = $('#submit-btn'),
        nickObj = $('#nick'),
        contentObj = $('#content');

    /**
     * Chat Item Module
     * @param nick
     * @param content
     * @returns {string}
     */
    var chatItemModule = function(nick, content){
        return '\
        <div class="chat-item"> \
            <span class="chat-user">' + nick + '</span>:\
            <span class="chat-content">' + content + '</span>\
        </div>';
    }

    var polling = function(){
        var action = 'polling',
            last_mid = $.cookie('polling');
        if (!last_mid) last_mid = 0;
        $.ajax({
            type: 'POST',
            url: client,
            dataType: 'json',
            data: { action: action, last_mid: last_mid },
            success: function (data){
                if (data.status === 0) {
                    $.each(data.chatItems, function(mid, chatItem){
                        chatViewObj.prepend(chatItemModule(chatItem.nick, chatItem.content));
                    });
                    $.cookie('polling', data.counter);
                }
            }
        });
    }

    /**
     * Initialize layouts and actions
     */
    var init = function(){
        $.cookie('polling', 0);
        //post a chat
        submitBtnObj.click(function(){
            var action = 'message',
                nick = nickObj.val(),
                content = contentObj.val();
            $.ajax({
                type: 'POST',
                url: client,
                dataType: 'json',
                data: { action: action, nick: nick, content: content },
                success: function (data){
                    if (data.status === 0) {
                        //I'll have to deal with the fucking scroll chat window if use append()
                        chatViewObj.prepend(chatItemModule(nick, content));
                        $.cookie('polling', data.mid);
                    } else {
                        alert('Invalid nick or content');
                    }
                    contentObj.val('');
                }
            });
            return false;
        })
        //polling
        polling();
        setInterval(polling, 5000);
    }

    init();
});