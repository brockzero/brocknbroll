<?php
  require "header.php";
?>
<div class="view-frame">
  <div id="wordVue">
    <h3 style="text-align:center;">{{word.title}}</h3>
    <div v-html="word.article">
      
    </div>
  </div>
</div>
<script>
  $(function(){
    
    function model(data)
    {
      self = this;
      self.category = data.category || '';
      self.createdDate = data.createdDate || '';
      self.id = data.id;
      self.article = data.article || '',
      self.title = data.title || '';
      self.user = data.user || '';
      self.url = data.url || '';

      return self;
    }

    var word = new Vue({
      el: '#wordVue',
      data: {
        currentBranch: 'dev',
        word: model
      },
      created: function () {
        this.fetchData();
      },
      methods: {
        fetchData: function () {
          var self = this;
          $.ajax({
              url: "/ajaxPhp/GetWord.php",
              data: { 
                page: "<?php echo $_GET['page'] ?>"
              },
              cache: false,
              type: "GET",
              success: function(response) {
                self.word = model(JSON.parse(response));
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