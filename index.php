<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>tgp 查询</title>
  <script src="http://192.168.1.251/static/src/js/lib/jquery.js"></script>
  <script src="http://192.168.1.251/static/src/js/lib/underscore.js"></script>
  <script src="http://192.168.1.251/static/src/js/lib/backbone.js"></script>
  <style>
    body{
      background: #eee;
    }
    .panel{
      margin:10px;
      float:left;
      min-height: 40px;
      padding: 10px;
      width:400px;
      background: #fff;
    }
  </style>
</head>
<body>
  <div id="search" class="panel">
    <div><input type="text" name="name" value="杀完回家搬砖"></div>
    <div><button class="button">查询</button></div>
  </div>
  
  <div id="userlist" class="panel"></div>

  <div id="history" class="panel"></div>

  <div id="gamedetail" class="panel"></div>

  <script>
    var $body = $(document.body);
    var $search = $("#search");
    var $userlist = $("#userlist");
    var $history = $("#history");
    var $gamedetail = $("#gamedetail");
    $search.on('click','.button',function(){
      var name = $search.find('input[name="name"]').val();
      $.getJSON('/api.pallas.tgp.qq.com/core/search_player?key='+name)
      .done(function(res){
        $userlist.html(userlist_tmp(res));
      });
    });

    $body.on('click','.tohistory',function(){
      var data = $(this).data();
      $.getJSON('/api.pallas.tgp.qq.com/core/tcall?dtag=profile&p=[[3,{"qquin":"'+data.qquin+'","area_id":"'+data.area_id+'","bt_num":"0","bt_list":[],"champion_id":0,"offset":0,"limit":8,"mvp_flag":-1}]]')
      .done(function(res){
        $history.html(history_tmp({data:res.data[0].battle_list,area_id:data.area_id}));
      });
    });

    $body.on('click','.todetail',function(){
      var data = $(this).data();
      $.getJSON('/api.pallas.tgp.qq.com/core/tcall?dtag=profile&p=[[4,{"area_id":"'+data.area_id+'","game_id":"'+data.game_id+'"}]]')
      .done(function(res){
        $gamedetail.html(gamedetail_tmp({gamer_records:res.data[0].battle.gamer_records,area_id:data.area_id}));
      })
    });

    var userlist_tmp = _.template([
      '<%_.each(data,function(item){%>',
      '<div>',
        '<img src="<%= item.icon_id %>">',
        '<a class="tohistory" href="javascript:;" data-qquin="<%= item.qquin %>" data-area_id="<%= item.area_id %>"><%= item.name %></a>',
        '<span><%= item.area_id %></span>',
      '</div>',
      '<% }); %>'
    ].join(''));

    var history_tmp = _.template([
      '<%_.each(data,function(item){%>',
      '<div>',
        '<a class="todetail" href="javascript:;" data-game_id="<%= item.game_id %>" data-area_id="<%= area_id %>"><%= item.win == 2 ? "失败" : "胜利" %></a>',
        '<span><%= item.battle_time %></span>',
      '</div>',
      '<% }); %>'
    ].join(''));

    var gamedetail_tmp = _.template([
      '<%_.each(gamer_records,function(item){%>',
      '<div>',
        '<a class="tohistory" href="javascript:;" data-qquin="<%= item.qquin %>" data-area_id="<%= area_id %>"><%= item.name %></a>',
        '<span><%= item.champions_killed %>/<%= item.num_deaths %>/<%= item.assists %></span>',
      '</div>',
      '<% }); %>'
    ].join(''));

  </script>
</body>
</html>

