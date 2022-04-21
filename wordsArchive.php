<?php
  require "header.php";
?>
<div class="view-frame">
  <div id="wordVue">
    <h3 style="text-align:center;">Word Archives</h3>
    <form id="wordSearchForm" @submit.prevent="search">
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
        <tr v-for="item in wordArchives">
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
      console.log(data);
      self = this;
      self.createdDate = data.createdDate || '';
      self.fileName = data.fileName || '';
      self.id = data.id;
      self.keywords = data.keywords || '';
      self.title = data.title || '';
      self.user = data.user || '';
      self.url = "/words/" + data.url;
      return self;
    }

    var wordArchive = new Vue({
      el: '#wordVue',
      data: {
        currentBranch: 'dev',
        wordArchives: model,
        keywords: ''
      },
      created: function () {
        this.fetchData();
      },
      methods: {
        fetchData: function () {
          var self = this;
          $.ajax({
              url: "/ajaxPhp/WordArchives.php",
              data: {          
              },
              cache: false,
              type: "GET",
              success: function(response) {
                var responseJson = JSON.parse(response);
                self.wordArchives = $.map(responseJson, function (item) { return new model(item) });
                console.log(self.wordArchives);
              },
              error: function(xhr) {
                console.log(xhr);
              }
          });
        },
        search: function (event) {
            var self = this;
            $.ajax({
              url: "/ajaxPhp/WordArchives.php",
              data: { 
                keywords: self.keywords
              },
              cache: false,
              type: "POST",
              success: function(response) {
                var responseJson = JSON.parse(response);
                self.wordArchives = $.map(responseJson, function (item) { return new model(item) });
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