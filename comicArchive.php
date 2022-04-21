<?php
  require "header.php";
?>
<div class="view-frame">
  <div id="comicVue">
    <h3 style="text-align:center;">Comic Archives</h3>
    <form id="comicSearchForm" @submit.prevent="search">
      <div class="row collapse">
        <div class="small-11 columns">
          <input class="input-group-field" type="text" size="50" name="keywords" v-model="keywords" placeholder="Keyword Search">
        </div>
        <div class="small-1 columns">
          <!--<a v-on:click="search" class="button postfix">Search</a>-->
          <input class="button postfix" type="submit" value="Search">
        </div>
      </div>
    </form>
    <table>
      <thead>
        <tr>
          <th>Comic</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in comicArchives">
          <td><a v-bind:href="item.url" v-html="item.title"></a></td>
          <td>{{item.createdDate}}</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
<script>
  $(function(){
    
    function model(data)
    {
      self = this;
      self.createdDate = data.createdDate || '';
      self.fileName = data.fileName || '';
      self.id = data.id;
      self.keywords = data.keywords || '';
      self.title = data.title || '';
      self.user = data.user || '';
      self.url = "comic/" + self.id;
      return self;
    }

    var comicArchive = new Vue({
      el: '#comicVue',
      data: {
        currentBranch: 'dev',
        comicArchives: model,
        keywords: ''
      },
      created: function () {
        this.fetchData();
      },
      methods: {
        fetchData: function () {
          var self = this;
          $.ajax({
              url: "ajaxPhp/ComicArchives.php",
              data: {          
              },
              cache: false,
              type: "GET",
              success: function(response) {
                var responseJson = JSON.parse(response);
                self.comicArchives = $.map(responseJson, function (item) { return new model(item) });
              },
              error: function(xhr) {
                console.log(xhr);
              }
          });
        },
        search: function (event) {
            var self = this;
            $.ajax({
              url: "ajaxPhp/ComicArchives.php",
              data: { 
                keywords: self.keywords
              },
              cache: false,
              type: "POST",
              success: function(response) {
                var responseJson = JSON.parse(response);
                self.comicArchives = $.map(responseJson, function (item) { return new model(item) });
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
?>