var vue = new Vue({
  el: '#app',
  data: {
    cpu: [],
    cpuSettings: {
      scale: [true, true]
    },
    memory: [],
    memorySetting: {},
    memoryColors: ['#fa6e86', '#19d4ae'],
    tcp: [],
    tcpSetting: [],
    tcpArr: [],
    tcpWidth: "100%",
  },
  mounted: function () {
    this.getCPUData();
    this.getMemoryData();
    this.getTCPData();
  },
  created: function () {
  },
  methods: {
    getCPUData: function () {
      jQuery.ajax({
        type: 'Get',
        url: "get.php?target=cpu",
        datatype: "json",
        success: function (data) {
          var data = JSON.parse(data);
          vue.cpu = {columns: data.column, rows: data.result};
        },
        failed: function () {
          vue.cpu = {columns: [], rows: []};
        }
      })
    },
    getMemoryData: function () {
      jQuery.ajax({
        type: 'Get',
        url: "get.php?target=memory",
        datatype: "json",
        success: function (data) {
          var data = JSON.parse(data);
          vue.memory = {columns: data.column, rows: data.rows};
          vue.memorySetting = {
            dimension: data.memorySetting.dimension,
            metrics: data.memorySetting.metrics,
            xAxisType: data.memorySetting.xAxisType,
            xAxisName: data.memorySetting.xAxisName,
            stack: data.memorySetting.stack,
            label: data.memorySetting.label
          };
        },
        failed: function () {
          vue.memory = {columns: [], rows: []};
        }
      })
    },
    getTCPData: function () {
      jQuery.ajax({
        type: 'Get',
        url: "get.php?target=tcp",
        datatype: "json",
        success: function (data) {
          var data = JSON.parse(data);
          vue.tcp = [];
          vue.tcpSetting = [];
          for (var i = 0; i < data.length; i++) {
            vue.tcp[i] = new Object();
            vue.tcp[i].columns = data[i].columns;
            vue.tcp[i].rows = data[i].rows;
            vue.tcpSetting[i] = data[i].tcpSettings;
            vue.tcpArr[i] = i;
          }
        },
        failed: function () {
          vue.tcp = {};
        }
      })
    }

  }
})

