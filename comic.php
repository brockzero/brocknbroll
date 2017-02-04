<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Brock n Broll</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
  <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css"> -->
  <link rel="stylesheet" href="css/app.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
  <script src="js/vue.js"></script>

</head>
<body>
  <?php
    echo $_GET['page'] ?? 'empty';
  ?>
  <div class="container">
    <div class="row" id="mainBody">
      <div class="col-md-1 menu">
        <div>
          <ul class="nav navbar-nav">
            <li><a href="#">Comic</a></li>
            <li><a href="#">Words</a></li>
            <li><a href="#">Contact</a></li>
            <li><a href="#">About</a></li>
          </ul>
        </div>
      </div>
      <div class="col-md-11" id="mainContent">
        <div class="view-frame">
			<div id="comic" v-if="items">
				<div>{{items.fileName}}</div>
				<div>{{items.user}}</div>
			</div>
		</div>
      </div>
    </div>
  </div>
  <script>
  $(function(){
    
    var demoList = new Vue({

      el: '#comic',

      data: {
        currentBranch: 'dev',
        items: null
      },

      created: function () {
        this.fetchData();
      },

      methods: {
        fetchData: function () {
        var self = this;
        $.ajax({
            url: "comic/comic.php",
            data: { 
                  page: "<?php echo $_GET['page'] ?? '0'; ?>"
            },
            cache: false,
            type: "GET",
            success: function(response) {
              self.items = JSON.parse(response);
              console.log(response);
            },
            error: function(xhr) {
              console.log(xhr);
            }
        });


       // $.get( apiURL, function( data ) {
       //     self.items = JSON.parse(data);
       //     console.log(data);
       //     console.log(self.items);
       // });
        }
      }
    });
  });
  </script>
</body>
</html>