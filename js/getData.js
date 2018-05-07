var vue = new Vue({
  el: '#app',
  data: {
    cpu: [],
    cpuSettings: {
      scale: [true, true],
    },
    memory: [],
    memorySetting: {},
    memoryColors: ['#fa6e86', '#19d4ae'],
    tcp: [],
    tcpSetting: [],
    tcpArr: [],
    tcpWidth: "98%",
    myArray: [
      {name:'1','id':'1'}
    ]
  },
  mounted: function () {
    // this.getCPUData();
    // this.getMemoryData();
    // this.getTCPData();
    // :title="test"
    // :data-zoom="dataZoom"
  },
  created: function () {
    this.ctitle = "title";
    this.chartData = {
      columns: ['日期', '成本', '利润'],
      rows: [
        {'日期': '1月1日', '成本': 15, '利润': 12},
        {'日期': '1月2日', '成本': 12, '利润': 25},
        {'日期': '1月3日', '成本': 21, '利润': 10},
        {'日期': '1月4日', '成本': 41, '利润': 32},
        {'日期': '1月5日', '成本': 31, '利润': 30},
        {'日期': '1月6日', '成本': 71, '利润': 55}
      ]
    };
    this.dataZoom = [
      {
        type: 'slider',
        start: 0,
        end: 20
      }
    ];
    this.toolbox = {
      feature: {
        magicType: {type: ['line', 'bar']},
        saveAsImage: {}
      }
    };
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

