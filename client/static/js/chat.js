$(document).ready(function(){
    //config
    var host = 'http://127.0.0.1';
    //var host = 'http://ryan.com';
    var client = host + '/client/client.php';

    //objects
    var channelListObj = $('#channel-list'),
        chatViewObj = $('#chat-view'),
        channelAddNameObj = $('#channel-add-name'),
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
            <div class="pure-u-3-4">\
                <h5 class="channel-header">CHANNEL</h5>\
                <h4 class="channel-name">' + channelName + '</h4>\
            </div>\
        </div>';
    };

    /**
     * Switch to channel `cid`
     * @param cid
     */
    var selectChannel = function(cid){
        unSelectCurrentChannel();
        var channelObj  = channelListObj.find('[data-channel="' + cid + '"]');
        channelObj.addClass('channel-entry-selected');
        $.cookie('channel', cid);
        $.cookie('polling', 0);
        clearChatView();
        polling();
    };

    /**
     * Cancel current channel
     */
    var unSelectCurrentChannel = function(){
        var cid = $.cookie('channel');
        var channelObj  = channelListObj.find('[data-channel="' + cid + '"]');
        channelObj.removeClass('channel-entry-selected');
    };

    /**
     * Polling new messages
     */
    var polling = function(){
        var action = 'polling',
            channel = $.cookie('channel'),
            last_mid = $.cookie('polling');
        $.ajax({
            type: 'POST',
            url: client,
            dataType: 'json',
            data: { action: action, channel: channel, last_mid: last_mid },
            success: function (data){
                if (data.status === 0) {
                    $.each(data.chatItems, function(mid, chatItem){
                        chatViewObj.prepend(chatItemModule(chatItem.nick, chatItem.content));
                    });
                    $.cookie('polling', data.counter);
                }
            }
        });
    };

    /**
     * Clear chat view when channel switches
     */
    var clearChatView = function(){
        chatViewObj.html('');
    };

    /**
     * Initialize layouts and actions
     */
    var init = function(){
        //initial cookies
        $.cookie('channel', 0);
        $.cookie('polling', 0);

        //post a chat
        submitBtnObj.click(function(){
            var action = 'message',
                channel = $.cookie('channel'),
                nick = nickObj.val(),
                content = contentObj.val();
            $.ajax({
                type: 'POST',
                url: client,
                dataType: 'json',
                data: { action: action, channel: channel, nick: nick, content: content },
                success: function (data){
                    if (data.status === 0) {
                        //I'll have to deal with the fucking scroll chat window if use append()
                        chatViewObj.prepend(chatItemModule(nick, content));
                        $.cookie('polling', data.mid);
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
        //switch channel
        channelListObj.on('click', '.channel-entry', function (){
            var nextChannel = $(this).data('channel');
            selectChannel(nextChannel);
        });
        //polling
        polling();
        setInterval(polling, 5000);
    };

    init();
});