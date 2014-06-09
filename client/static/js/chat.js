$(document).ready(function(){
    //config
    var host = 'http://127.0.0.1';
    var client = host + '/client.php';

    //objects
    var channelListObj = $('#channel-list'),
        chatViewObj = $('#chat-view'),
        userListObj = $('#user-list'),
        channelAddNameObj = $('#channel-add-name'),
        submitBtnObj = $('#submit-btn'),
        chatBoxNameObj = $('#chat-box-name'),
        nickObj = $('#nick'),
        toNickObj = $('#toNick'),
        contentObj = $('#content');

    //Polling Lock to ensure that polling() should call once at a time
    var pollingLock = false;


    /**
     * Escape HTML tags so that js can not be embedded
     * @param string
     * @returns {string}
     */
    function escapeHtml(string) {
        var entityMap = {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': '&quot;',
            "'": '&#39;',
            "/": '&#x2F;'
        };
        return String(string).replace(/[&<>"'\/]/g, function (c){
            return entityMap[c];
        });
    }
    /**
     * Chat Item Module
     * @param nick
     * @param content
     * @returns {string}
     */
    var chatItemModule = function(nick, content, toNick){
        var toNickHTML = '';
        if (toNick != '')
            toNickHTML = ' to <span class="chat-user">' + toNick + '</span>';
        return '\
        <div class="chat-item">\
            <span class="chat-user">' + nick + '</span>' +
            toNickHTML +
            ': <span class="chat-content">' + escapeHtml(content) + '</span>\
        </div>';
    };

    /**
     * @param cid
     * @param channelName
     * @returns {string}
     */
    var channelItemModule = function(cid, channelName){
        return '\
        <div class="channel-entry pure-g" data-channel="' + cid + '">\
            <div class="pure-u">\
                <img class="channel-avatar" src="static/img/octocat.jpeg">\
            </div>\
            <div class="pure-u-5-8">\
                <h5 class="channel-header">CHANNEL</h5>\
                <h4 class="channel-name">' + channelName + '</h4>\
            </div>\
            <div class="channel-delete">x</div>\
        </div>';
    };

    var userItemModule = function(nick){
        return '\
        <div class="user-item clearfix">\
            <img class="user-avatar" src="static/img/octocat.jpeg">\
            <div class="user-name">' + nick + '</div>\
        </div>';
    };

    /**
     * Switch to channel `cid`
     * @param cid
     */
    var selectChannel = function(cid){
        unSelectCurrentChannel();
        var channelObj = channelListObj.find('[data-channel="' + cid + '"]');
        channelObj.addClass('channel-entry-selected');
        chatBoxNameObj.text(channelObj.find('.channel-name').text());
        $.cookie('channel', cid);
        $.cookie('polling', 0);
        clearChatView();
        listUsers();
        polling();
    };

    /**
     * Cancel current channel
     */
    var unSelectCurrentChannel = function(){
        var cid = $.cookie('channel');
        var channelObj = channelListObj.find('[data-channel="' + cid + '"]');
        channelObj.removeClass('channel-entry-selected');
    };

    /**
     * Remove from list
     * @param cid
     */
    var removeChannel = function(cid){
        var channelObj = channelListObj.find('[data-channel="' + cid + '"]');
        channelObj.remove();
    }

    /**
     * Clear chat view when channel switches
     */
    var clearChatView = function(){
        chatViewObj.html('');
    };

    /**
     * Polling new messages
     */
    var polling = function(){
        if (pollingLock === true) return;
        pollingLock = true;
        var action = 'polling',
            channel = $.cookie('channel'),
            last_mid = $.cookie('polling'),
            nick = $.cookie('nick');
        if (!nick) nick = '';
        $.ajax({
            type: 'POST',
            url: client,
            dataType: 'json',
            data: { action: action, channel: channel, last_mid: last_mid, nick: nick },
            success: function (data){
                if (data.status === 0) {
                    $.each(data.chatItems, function(mid, chatItem){
                        if (chatItem.toNick == '' || chatItem.nick == nick || chatItem.toNick == nick) {
                            chatViewObj.prepend(chatItemModule(chatItem.nick, chatItem.content, chatItem.toNick));
                            last_mid = mid;
                        }
                    });
                    $.cookie('polling', last_mid);
                }
                pollingLock = false;
            }
        });
    };

    /**
     * Request for a list of all channels
     */
    var listChannels = function(){
        var action = 'listChannels';
        $.ajax({
            type: 'POST',
            url: client,
            dataType: 'json',
            data: { action: action },
            success: function (data){
                if (data.status === 0) {
                    channelListObj.html('');
                    $.each(data.channels, function(cid, name){
                        channelListObj.prepend(channelItemModule(cid, name));
                    });
                    selectChannel($.cookie('channel'));
                }
            }
        });
    };

    var listUsers = function(){
        var action = 'listUsers',
            channel = $.cookie('channel');
        $.ajax({
            type: 'POST',
            url: client,
            dataType: 'json',
            data: { action: action, channel: channel },
            success: function (data){
                if (data.status === 0) {
                    userListObj.html('');
                    $.each(data.users, function(id, nick){
                        userListObj.prepend(userItemModule(nick));
                    });
                }
            }
        });
    };

    /**
     * Request for deleting a channel
     * @param cid
     */
    var deleteChannel = function(cid){
        var action = 'deleteChannel';
        $.ajax({
            type: 'POST',
            url: client,
            dataType: 'json',
            data: { action: action, channel: cid },
            success: function (data){
                if (data.status === 0) {
                    removeChannel(cid);
                }
            }
        });
    };

    /**
     * Initialize layouts and actions
     */
    (function(){
        //initial cookies
        $.cookie('channel', 0);
        $.cookie('polling', 0);

        //post a chat
        submitBtnObj.click(function(){
            var action = 'message',
                channel = $.cookie('channel'),
                nick = nickObj.val(),
                content = contentObj.val(),
                toNick = toNickObj.val();
            if (!toNick) toNick = '';
            $.ajax({
                type: 'POST',
                url: client,
                dataType: 'json',
                data: { action: action, channel: channel, nick: nick, content: content, toNick: toNick },
                success: function (data){
                    if (data.status === 0) {
                        //I'll have to deal with the fucking scroll chat window if use append()
                        var curPolling = parseInt($.cookie('polling'));
                        if (curPolling + 1 === data.mid) {
                            chatViewObj.prepend(chatItemModule(nick, content, toNick));
                            $.cookie('polling', data.mid);
                        }
                        $.cookie('nick', nick);
                    } else {
                        alert('Failed. Notice that nick can not be empty.');
                    }
                    contentObj.val('');
                }
            });
            return false;
        });
        //add a new channel
        $('#channel-add-btn').click(function (){
            var action = 'channelAdd',
                channelName = channelAddNameObj.val();
            $.ajax({
                type: 'POST',
                url: client,
                dataType: 'json',
                data: { action: action, channelName: channelName },
                success: function(data){
                    if (data.status === 0) {
                        channelListObj.prepend(channelItemModule(data.cid, channelName));
                        selectChannel(data.cid);
                        channelAddNameObj.val('');
                    } else {
                        alert('Failed. Notice that channel name can not be empty.');
                    }
                }
            });
            return false;
        });
        //delete channel
        channelListObj.on('click', '.channel-entry .channel-delete', function (){
            var targetChannel = $(this).parent().data('channel');
            deleteChannel(targetChannel);

        });
        //switch channel
        channelListObj.on('click', '.channel-entry', function (){
            var nextChannel = $(this).data('channel');
            selectChannel(nextChannel);
        });
        //polling
        listChannels();
        setInterval(listChannels, 60000);
        listUsers();
        setInterval(listUsers, 10000);
        polling();
        setInterval(polling, 5000);
    })();
});