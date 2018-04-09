var vue = new Vue({
  el: '#app',
  data: {
    cpu: [],
    cpuSettings: {
      scale: [true,true]
    },
  },
  mounted: function () {
    this.getData();
  },
  created: function () {
  },
  methods: {
    getData: function () {
      jQuery.ajax({
        type: 'Get',
        url: "get.php",
        datatype: "json",
        success: function (data) {
          var data = JSON.parse(data);
          vue.cpu = {columns: data.column, rows: data.result};
        },
        failed: function () {
          vue.cpu = {columns: [], rows: []};
        }
      })
    }
  }
})
