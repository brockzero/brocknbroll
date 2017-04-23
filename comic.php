<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Brock n Broll</title>
  <link rel="stylesheet" href="css/foundation.css">
  <link rel="stylesheet" href="css/app.css">
  <!--<link rel="stylesheet" href="css/site_main.css">-->
  <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
  <script src="js/vue.js"></script>
</head>
<body>
<div class="row main">
  <div class="large-2 columns" id="siteNav">
    <ul class="menu vertical">
      <li><a href="comic.php">Comic</a></li>
      <!--<li><a href="#">Words</a></li>-->
      <li><a href="#">Contact</a></li>
      <li><a href="#">About</a></li>
    </ul>
    <div>
      <img src="img/brock-n-broll-banner.png" alt="Brock n Broll" />
      <h1>Brock 'N Broll</h1>
    </div>
  </div>
  <div class="large-10 columns full-height" id="mainContent">
    <div class="view-frame">
      <div id="comic">
        <h3 style="text-align:center;">{{comic.title}}</h3>
        <div class="comicNav">
          <ul class="menu expanded">
            <li><a v-bind:href="comic.url+comic.pagingFirst">First</a></li> 
            <li><a v-bind:href="comic.url+comic.pagingPrevious">Previous</a></li>  
            <li>{{comic.id}}</li>  
            <li><a v-bind:href="comic.url+comic.pagingNext">Next</a></li> 
            <li><a v-bind:href="comic.url+comic.pagingLast">Last</a></li> 
          </ul>
        </div>
        <div><img v-bind:alt="comic.altAttr" v-bind:src="comic.fileName" v-bind:title="comic.titleAttr" ></div>
        <div class="comicNav">
          <ul class="menu expanded">
            <li><a v-bind:href="comic.url+comic.pagingFirst">First</a></li> 
            <li><a v-bind:href="comic.url+comic.pagingPrevious">Previous</a></li>
            <li>{{comic.id}}</li>  
            <li><a v-bind:href="comic.url+comic.pagingNext">Next</a></li> 
            <li><a v-bind:href="comic.url+comic.pagingLast">Last</a></li> 
          </ul>
        </div>
        <div class="row">
          <div class="large-12 columns">
            <div class="comicDescription">
              <div v-html="comic.description"></div>
              <div>&mdash;{{comic.user}}</div>
            </div>
          </div>
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
      self = this;
      self.altAttr = data.altAttr || '';
      self.category = data.category || '';
      self.createdDate = data.createdDate || '';
      self.description = data.description || '';
      self.fileName = 'comic/content/' + data.fileName || '';
      self.id = data.id;
      self.keywords = data.keywords || '';
      self.pagingFirst = data.pagingFirst || '';
      self.pagingLast = data.pagingLast || '';
      self.title = data.title || '';
      self.titleAttr = data.titleAttr || '';
      self.user = data.user || '';
      self.pagingNext = data.pagingLast;
      self.pagingPrevious = data.pagingFirst;

      if(self.id < self.pagingLast) {
        self.pagingNext = self.id + 1;
      }

      if(self.id > 1) {
        self.pagingPrevious = self.id - 1;
      } 

      self.url = "comic.php?page=";
      return self;
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
          var self = this;
          $.ajax({
              url: "comic/comic.php",
              data: { 
                page: "<?php echo $_GET['page'] ?? '0'; ?>"
              },
              cache: false,
              type: "GET",
              success: function(response) {
                self.comic = model(JSON.parse(response));
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