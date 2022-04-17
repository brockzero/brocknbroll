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
            <!--<li><a href="#">Words</a></li>-->
            <li><a href="#">Contact</a></li>
            <li><a href="#">About</a></li>
          </ul>
        </div>
      </div>
      <div class="col-md-11" id="mainContent">
        <div class="view-frame">
          <div id="comic">
            <h2>{{comic.title}}</h2>
            <div class="paging">
              <a v-bind:href="comic.url+comic.pagingFirst">First</a> | 
              <a v-bind:href="comic.url+comic.pagingPrevious">Previous</a> |  
              <a v-bind:href="comic.url+comic.pagingNext">Next</a> | 
              <a v-bind:href="comic.url+comic.pagingLast">Last</a>
            </div>
            <div>
              <img v-bind:alt="comic.altAttr" v-bind:src="comic.fileName" v-bind:title="comic.titleAttr" >
            </div>
            <div class="paging">
              <a v-bind:href="comic.url+comic.pagingFirst">First</a> | 
              <a v-bind:href="comic.url+comic.pagingPrevious">Previous</a> |  
              <a v-bind:href="comic.url+comic.pagingNext">Next</a> | 
              <a v-bind:href="comic.url+comic.pagingLast">Last</a>
            </div>
            <div>{{comic.user}}</div>
            <div v-html="comic.description"></div>
          </div>
		    </div>
      </div>
    </div>
  </div>
  <script>
  $(function(){
    
    function model(data)
    {
      console.log(data);
      let that = this;
      that.altAttr = data.altAttr || '';
      that.category = data.category || '';
      that.createdDate = data.createdDate || '';
      that.description = data.description || '';
      that.fileName = 'comic/content/' + data.fileName || '';
      that.id = data.id;
      that.keywords = data.keywords || '';
      that.pagingFirst = data.pagingFirst || '';
      that.pagingLast = data.pagingLast || '';
      that.title = data.title || '';
      that.titleAttr = data.titleAttr || '';
      that.user = data.user || '';
      that.pagingNext = data.pagingLast;
      that.pagingPrevious = data.pagingFirst;

      if(that.id < that.pagingLast) {
        that.pagingNext = that.id + 1;
      }

      if(that.id > 1) {
        that.pagingPrevious = that.id - 1;
      } 

      that.url = "comic.php?page=";
      return that;
    }

    var comic = new Vue({
      el: '#comic',
      data: {
        currentBranch: 'dev',
        comic: model
      },
      created: function () {
        this.fetchData();
      },
      methods: {
        fetchData: function () {
          let that = this;
          $.ajax({
              url: "comic/comic.php",
              data: { 
                page: "<?php echo $_GET['page'] ?? '0'; ?>"
              },
              cache: false,
              type: "GET",
              success: function(response) {
                that.comic = model(JSON.parse(response));
              },
              error: function(xhr) {
                console.log(xhr);
              }
          });
        }
      }
    });
  });
  </script>
</body>
</html>