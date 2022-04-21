<?php
  require "header.php";
?>
<div class="view-frame">
  <div id="comicVue">
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
    <div>
      <img v-bind:alt="comic.altAttr" v-bind:src="comic.fileName" v-bind:title="comic.titleAttr">
    </div>
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
<script>
  $(function() {

    function model(data) {
      self = this;
      //self.altAttr = data.altAttr || '';
      self.category = data.category || '';
      //<p>{{comic.createdDate}}</p>
      //self.createdDate = data.createdDate || '';
      self.description = data.description || '';
      self.fileName = 'comic/' + data.fileName || '';
      self.id = data.id;
      self.keywords = data.keywords || '';
      self.pagingFirst = data.pagingFirst || 1;
      self.pagingLast = data.pagingLast || 1;
      self.title = data.title || '';
      self.titleAttr = data.titleAttr || '';
      self.user = data.user || '';
      self.pagingNext = data.pagingLast;
      self.pagingPrevious = data.pagingFirst;

      if (self.id < self.pagingLast) {
        self.pagingNext = self.id + 1;
      } else {
        self.pagingNext = self.id;
      }

      if (self.id > 1) {
        self.pagingPrevious = self.id - 1;
      }

      self.url = "comic/";
      return self;
    }

    var comic = new Vue({
      el: '#comicVue',
      data: {
        currentBranch: 'dev',
        comic: model
      },
      created: function() {
        this.fetchData();
      },
      methods: {
        fetchData: function() {
          var self = this;
          $.ajax({
            url: "ajaxPhp/GetComic.php",
            data: {
              page: "<?php echo $_GET['page'] ?? '0'; ?>"
            },
            cache: false,
            type: "GET",
            success: function(response) {
              console.log(response);
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
<?php
  require "footer.php";
