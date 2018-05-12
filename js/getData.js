var vue = new Vue({
  el: '#app',
  data: {
    cpu: [],
    cpuSettings: {
      scale: [true, true],
    },
    cpuWidth: '98%',
    memory: [],
    memorySetting: {},
    memoryColors: ['#fa6e86', '#19d4ae'],
    tcp: [{"columns":["type","192.168.171.140:9100"],"rows":[{"type":"链接信息错误","192.168.171.140:9100":"0"},{"type":"监听断开","192.168.171.140:9100":"0"},{"type":"连接超时","192.168.171.140:9100":"10"},{"type":"重连丢失","192.168.171.140:9100":"0"},{"type":"异常关闭","192.168.171.140:9100":"2"}]}],
    tcpSetting: [],
    tcpArr: [0],
    tcpWidth: "98%",
    myArray: [
      {name: '1', 'id': '1'}
    ]
  },
  mounted: function () {
    this.createDialogs();
  },
  created: function () {
    this.getTCPData();
    this.getCPUData();
    this.getMemoryData();
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
          // vue.tcp = {};
        }
      })
    },
    createDialogs: function () {
      $("#cpu").dialog({
        autoOpen: true,
        show: {
          effect: "blind",
          duration: 1000
        },
        hide: {
          effect: "explode",
          duration: 1000
        },
        width: this.cpuWidth,
        resizable: {
          grid: 50,
          handles: "all",
          aspectRatio: true,
        },
        title: "CPU",
        resizeStart: function () {

        },
        resizeStop: function () {

        },
        resize: function () {
          vue.cpuWidth = $("div[aria-describedby='cpu']").width()+'';
        }
      });
      $("div[aria-describedby='cpu']").resizable({
        aspectRatio: true,
        handles: "e"
      });
      $("#tcp").dialog({
        autoOpen: true,
        show: {
          effect: "blind",
          duration: 1000
        },
        hide: {
          effect: "explode",
          duration: 1000
        },
        width: this.tcpWidth,
        resizable: {
          grid: 50,
          handles: "all",
          aspectRatio: true,
        },
        title: "TCP",
        resizeStart: function () {

        },
        resizeStop: function () {

        },
        resize: function () {
          vue.tcpWidth = $("div[aria-describedby='tcp']").width()+'';
        }
      });
      $("div[aria-describedby='tcp']").resizable({
        aspectRatio: true,
        handles: "e"
      });
      $("#memory").dialog({
        autoOpen: true,
        show: {
          effect: "blind",
          duration: 1000
        },
        hide: {
          effect: "explode",
          duration: 1000
        },
        width: this.tcpWidth,
        resizable: {
          grid: 50,
          handles: "all",
          aspectRatio: true,
        },
        title: "内存",
        resizeStart: function () {

        },
        resizeStop: function () {

        },
        resize: function () {
          vue.cpuWidth = $("div[aria-describedby='cpu']").width()+'';
        }
      });
      $("div[aria-describedby='memory']").resizable({
        aspectRatio: true,
        handles: "e"
      });
    }

  }
});

