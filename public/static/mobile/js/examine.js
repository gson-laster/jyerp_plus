

  $("#examine").click(function() {
    if (url) {
      $(".baseLayer").css('display', 'block');
      var htm = "";
      $.ajax({
        type: "post",
        url: url,
        async: true,
        success: function(res) {
          var $__1 = res,
              title = $__1.title,
              list = $__1.list;
          for (var i = 0; i < list.length; i++) {
            htm += "<div class=\"even\"><div class=\"title\">" + list[i][res.name] + "</div><ul>";
            var l = list[i];
            for (var j in l) {
              htm += ("<li>       <p>" + title[j] + "</p>       <p>" + l[j] + "</p>       </li>");
            }
            htm += "<div class=\"clearfix\">             </div>       </ul></div>";
          }
          $(".layer_list").empty();
          $(".layer_list").append(htm);
        }
      });
    }
  });
  function layer_confirm() {
    $(".baseLayer").css('display', 'none');
  }

