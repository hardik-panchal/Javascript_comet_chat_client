<script type="text/javascript">
    
    
    function enterSmiley(el,iconTxt){
        $(el).parent().hide();
        var val = $(el).parent().next().find('input:first').val()
        var val = $(el).parent().next().find('input:first').val(val + iconTxt)
    }
    function chatPoll(){
        $.ajax({
            type:"POST",
            url:"<?php print _CHAT_SERVER ?>chat.php",
            data:{chat:1,session:chatSession,user_id:_u.user_id},
            success:function(remoteContent){
                if(remoteContent != ''){
                    var chatData = $.parseJSON(remoteContent);
                
                    $.each(chatData,function(k,v){
                        if(typeof chatSession[k] == "undefined"){
                            chatSession[k] = {toName:userDS[v[0][1]].username};
                            createChatWindow(k);
                        }
                        $("#chat_"+k).show();
                        $("#chat_"+k+" .chatWindowText").html('');
                        $.each(v,function(key,value){
                            var uname = _getIMG(value[1]);
                            var text = _getTxt(value[0]);
                            var time = _getTime(value[2]);
                            $("#chat_"+k+" .chatWindowText").append(""+ uname + " "+ text + " " + time + "<hr />").scrollTop(10000);    
                        });
                    });
                }               
            } 
        });
    }
    
    var _getTxt = function(text){ 
        return "<div style='float:left;width:70px'>"+text+"</span>";
    }
    var _getTime = function(time){ 
        return "<div style='float:right;width:40px;color:red'>"+time+"</span>";
    }
    var _getIMG = function(id){ 
        var thumb = userDS[id].userimage;
        return "<img src='"+thumb+"' width=30 height=30 style='float:left;width:35px' />";
    }
    
    var chatSession = {}
    
    
    function initChat(id,name){
        $.ajax({
            type:"POST",
            url:"<?php print _CHAT_SERVER ?>chat/init.php",
            data:{init:1,from:_u.user_id,to:id,from_ip:'<?php print $_SERVER[REMOTE_ADDR] ?>'},
            success:function(remoteContent){
                var data = $.parseJSON(remoteContent);
                if(data.session != 0){
                    chatSession[data.session] = {from:_u.user_id,to:id,toName:name};
                    createChatWindow(data.session);
                }else{
                    showBottomWindowError("Cant start chat sesion");
                }
            } 
        });
    }
    
    function _totalChatWin(){
        return Object.keys(chatSession).length - 1;
    }
    
    function createChatWindow(session){
        $("#chatCanvas").append($(".chatWindow").clone());
        var left = _totalChatWin() * 312;
        var winEl = $(".chatWindow:last");
        winEl.css("left",left + "px").show();
        winEl.attr("id","chat_"+session);
        $(winEl).find("div.chatWindowTitleText").html(chatSession[session].toName);
        $(winEl).find("div.chatWindowClose").data("id",session);
        $(".chatWindowTextBoxEvent").not(".chatWindowTextBoxEventAdded").keydown(chatkd);
    }
    
    
    function killChat(el){
        var elSession = $(el).data("id");
        $("#chat_"+elSession).remove();
        killSession(elSession);
    }
    
    function killSession(session){
        delete chatSession[session];
        $.ajax({
            type:"POST",
            url:"<?php print _CHAT_SERVER ?>chat/kill.php",
            data:{kill:session},
            success:function(remoteContent){
                
            } 
        });
    }
    
    function doPostChat(txt,session,uid){
        $("#chat_"+session + " .chatWindowText").append("<br />"+_u.userName+": "+txt);
        var _data = [txt,session,uid];
        $.ajax({
            type:"POST",
            url:"<?php print _CHAT_SERVER ?>chat/post.php",
            data:{post:1,load:_data},
            success:function(remoteContent){
                
            } 
        });
    }
    
    function chatkd(e){
        if(e.keyCode == 13 && $(e.currentTarget).val() != ''){
            var session = ($(e.currentTarget).parent().parent().attr("id"));
            doPostChat($(e.currentTarget).val(),session.split("_")[1],_u.user_id);
            $(e.currentTarget).val('');
        }
    }

</script>
