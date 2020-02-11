define([
    'jquery',
    'underscore',
    'ko',
    'uiComponent'
], function ($, _, ko, Component) {
    'use strict';
    
    return Component.extend({
        defaults: {
            template: 'report/chart',
            
            provider: '${ $.provider }:data',
            
            imports: {
                comparison:      '${ $.provider }:data.comparison',
                rows:            '${ $.provider }:data.items',
                dimensionColumn: '${ $.provider }:data.dimensionColumn',
                columnsProvider: '${ $.columnsProvider }:elems',
                params:          '${ $.provider }:params'
            },
            
            exports: {},
            
            listens: {
                rows:            'initChart updateData',
                dimensionColumn: 'onChangeDimensionColumn',
                chartType:       'initChart',
                columns:         'updateScales updateData',
                comparison:      'updateData',
                columnsProvider: 'initColumns'
            },
            
            wrapSelector: '.report__chart-wrap',
            scaleTypes:   ['price', 'number'],
            
            colors: [
                '#97CC64',
                '#FF5A3E',
                '#FFD963',
                '#77B6E7',
                '#A9B9B8',
                '#DC9D6B'
            ]
        },
        
        initialize: function () {
            this._super();
            
            _.bindAll(this, 'setChartType', 'initColumns', 'initChart');
            
            if (this.chartType() === 'geo') {
                this.typeSwitcher = ['geo'];
            } else {
                this.typeSwitcher = ['bar', 'line'];
            }
            
            return this;
        },
        
        initObservable: function () {
            this._super()
                .observe({
                    rows:           [],
                    columns:        [],
                    chartType:      this.chartType === 'column' ? 'bar' : this.chartType,
                    visibleColumns: []
                });
            
            return this;
        },
        
        initChart: function () {
            if (this.chartType() === "geo") {
                this.initGeoChart();
            } else {
                this.initColumnChart();
            }
        },
        
        initColumnChart: function () {
            if (!document.getElementById('chart_canvas')) {
                return;
            }
            
            if (!this.getXLabels().length) {
                $(this.wrapSelector).hide();
                return;
            } else {
                $(this.wrapSelector).show();
            }
            
            if (this.chart) {
                return;
            }
            
            var canvas = document.getElementById('chart_canvas').getContext('2d');
            
            var options = {
                title:               {
                    display: false
                },
                legend:              {
                    display:  true,
                    position: 'right',
                    onClick:  function (e, legendItem) {
                        var column = _.find(this.columns(), {label: legendItem.text});
                        
                        column.isVisible = !column.isVisible;
                        
                        var index = legendItem.datasetIndex;
                        var ci = this.chart;
                        var meta = this.chart.getDatasetMeta(index);
                        meta.hidden = !column.isVisible;
                        
                        ci.update();
                        
                        this.columns.valueHasMutated();
                    }.bind(this)
                },
                responsive:          true,
                maintainAspectRatio: false
            };
            
            if (this.getScales()) {
                options.scales = this.getScales();
            }
            
            this.chart = new Chart(canvas, {
                type:    this.chartType() === "pie" ? "doughnut" : this.chartType(),
                options: options
            });
            
            this.updateScales();
            this.updateData();
        },
        
        updateData: function () {
            if (!this.chart) {
                return;
            }
            
            var chart = this.chart;
            
            var data = {
                labels:   this.getXLabels(),
                datasets: this.getDataSets()
            };
            
            if (this.chart.data !== data) {
                this.chart.data = data;
                this.chart.update(0, true);
                
                // dashed rectangle for comparison
                _.each(this.chart.data.datasets, function (set) {
                    if (set.xAxisID) {
                        if (set._meta[0]) {
                            _.each(set._meta[0].data, function (rectangle, index) {
                                rectangle.draw = function () {
                                    chart.chart.ctx.setLineDash([5, 2]);
                                    Chart.elements.Rectangle.prototype.draw.apply(this, arguments);
                                }
                            });
                        }
                    }
                });
            }
        },
        
        updateScales: function () {
            if (this.chart && this.chartType() !== 'pie') {
                _.each(this.scaleTypes, function (type) {
                    var scale = _.find(this.chart.options.scales.yAxes, {id: 'y-axis-' + type});
                    
                    scale.display = _.findIndex(this.columns(), {
                            type:      type,
                            isVisible: true
                        }) >= 0;
                }.bind(this));
                
                this.chart.update(0, true);
            }
        },
        
        getScales: function () {
            if (this.chartType() === 'pie') {
                return false;
            } else {
                var scales = {
                    xAxes: [
                        {
                            display:   true,
                            id:        'x-axis',
                            stacked:   true,
                            gridLines: {
                                drawOnChartArea: false
                            }
                        },
                        {
                            display:            false,
                            stacked:            true,
                            id:                 "x-axis-c",
                            inside:             true,
                            type:               'category',
                            categoryPercentage: 0.8,
                            barPercentage:      0.9,
                            gridLines:          {
                                offsetGridLines: true
                            }
                        }
                    ],
                    yAxes: []
                };
                
                _.each(this.scaleTypes, function (type) {
                    scales.yAxes.push({
                        display: true,
                        id:      'y-axis-' + type,
                        ticks:   {
                            beginAtZero: true
                        }
                    });
                });
                
                return scales;
            }
        },
        
        initGeoChart: function () {
            if (!document.getElementById('chart_div')) {
                return;
            }
            
            $('#chart_canvas').hide();
            $('#chart_div').css('height', '400px');
            
            var data = this.getData();
            
            if (!data) {
                $(this.wrapSelector).hide();
                return;
            }
            
            $(this.wrapSelector).show();
            
            data = google.visualization.arrayToDataTable(data);
            
            if (!this.geoChart) {
                this.geoChart = new google.visualization.GeoChart(document.getElementById('chart_div'));
            }
            
            var options = {
                legend:   {position: 'none'},
                colors:   this.getColors(),
                axes:     {
                    x: {0: {'label': ''}}
                },
                width:    '100%',
                height:   400,
                fontName: "'Open Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif"
            };
            
            if (this.params.filters && this.params.filters['sales_order_address|country']) {
                options['region'] = this.params.filters['sales_order_address|country'];
                options['resolution'] = 'provinces';
            }
            
            var view = new google.visualization.DataView(data);
            
            this.geoChart.draw(view, options);
        },
        
        getXLabels: function () {
            var labels = [];
            
            _.each(this.rows(), function (obj) {
                _.each(this.columns.filter('isDimension'), function (column) {
                    labels.push(this.getCellValue(column, obj) + "");
                }, this);
            }, this);
            
            return labels;
        },
        
        getDataSets: function () {
            var sets = [];
            
            if (this.chartType() === 'pie') {
                _.each(this.columns(), function (column) {
                    if (column.isInternal) {
                        return;
                    }
                    
                    var type = 'number';
                    
                    if (column.type === 'price') {
                        type = column.type;
                    }
                    
                    var set = {
                        label:           column.label,
                        stack:           column.index,
                        backgroundColor: [],
                        borderColor:     [],
                        borderWidth:     1,
                        data:            [],
                        hidden:          !column.isVisible
                    };
                    
                    _.each(this.rows(), function (row, i) {
                        var value = this.getCellValue(column, row);
                        set.data.push(value);
                        set.backgroundColor.push(Chart.helpers.color(this.getColor(i)).alpha(0.8).rgbString());
                        set.borderColor.push(Chart.helpers.color(this.getColor(i)).alpha(0.9).rgbString());
                    }, this);
                    
                    sets.push(set);
                }.bind(this));
            } else {
                _.each(this.columns(), function (column) {
                    if (column.isInternal) {
                        return;
                    }
                    
                    var type = 'number';
                    
                    if (column.type === 'price') {
                        type = column.type;
                    }
                    
                    var set = {
                        label:           column.label,
                        stack:           column.index,
                        backgroundColor: Chart.helpers.color(column.color).alpha(0.8).rgbString(),
                        borderColor:     Chart.helpers.color(column.color).alpha(0.9).rgbString(),
                        borderWidth:     1,
                        data:            [],
                        hidden:          !column.isVisible,
                        yAxisID:         "y-axis-" + type
                    };
                    var comparisonSet = {
                        label:           false,
                        stack:           column.index + '_c',
                        backgroundColor: Chart.helpers.color(column.color).alpha(0.3).rgbString(),
                        borderColor:     Chart.helpers.color(column.color).alpha(1).rgbString(),
                        borderWidth:     1,
                        data:            [],
                        hidden:          !column.isVisible,
                        yAxisID:         "y-axis-" + type,
                        xAxisID:         'x-axis-c'
                    };
                    
                    _.each(this.rows(), function (row) {
                        var value = this.getCellValue(column, row);
                        set.data.push(value);
                        
                        if (this.comparison) {
                            comparisonSet.data.push(this.getCellValue(column, row, 'c_'));
                        }
                    }, this);
                    
                    sets.push(set);
                    
                    if (this.comparison) {
                        sets.push(comparisonSet);
                    }
                }, this);
            }
            
            return sets;
        },
        
        getData: function () {
            var rows = [];
            
            var header = [];
            _.each(this.columns.filter('isDimension'), function (column) {
                header.push(column.label);
            });
            
            _.each(this.columns.filter('isVisible'), function (column) {
                header.push(column.label);
                
                if (this.comparison) {
                    header.push(column.label);
                }
            }, this);
            
            rows.push(header);
            
            if (header.length < 2) {
                return false;
            }
            
            _.each(this.rows(), function (obj) {
                var row = [];
                
                _.each(this.columns.filter('isDimension'), function (column) {
                    row.push(this.getCellValue(column, obj) + "");
                }, this);
                
                _.each(this.columns.filter('isVisible'), function (column) {
                    row.push(this.getCellValue(column, obj));
                    
                    if (this.comparison) {
                        row.push(this.getCellValue(column, obj, 'c_'));
                    }
                }, this);
                
                rows.push(row);
            }, this);
            
            if (rows.length < 2) {
                return false;
            }
            
            return rows;
        },
        
        getCellValue: function (column, row, prefix) {
            var index = column.index;
            
            if (prefix !== undefined) {
                index = prefix + index;
            }
            
            var value = row[index];
            
            var type = column.type;
            
            if (type === 'number' || type === 'price') {
                value = parseFloat(parseFloat(value).toFixed(2));
            } else if (type === 'date') {
                value = new Date(Date.parse(value));
            } else if (type === 'country') {
                value = value + '';
            } else {
                value = column.model.getLabel(row);
            }
            
            return value;
        },
        
        getColors: function () {
            var colors = [];
            
            _.each(this.columns.filter('visibleOnChart'), function (column) {
                colors.push(column.color);
            }, this);
            
            return colors;
        },
        
        setChartType: function (type) {
            this.chart.destroy();
            this.chart = null;
            
            this.chartType(type);
        },
        
        onChangeDimensionColumn: function () {
            this.initColumns();
            this.initChart();
        },
        
        initColumns: function () {
            this.columns([]);
            
            if (!$.isArray(this.vAxis)) {
                this.vAxis = [this.vAxis];
            }
            
            _.each(this.columnsProvider, function (column) {
                var isVisible = _.indexOf(this.vAxis, column.index) >= 0 && column.index !== this.dimensionColumn;
                
                var data = {
                    index:       column.index,
                    label:       column.label,
                    color:       column.color,
                    type:        column.valueType,
                    isVisible:   isVisible,
                    isDimension: column.index === this.dimensionColumn,
                    isInternal:  column.index === this.dimensionColumn || column.isHidden || !column.visible || column.index === 'actions',
                    model:       column
                };
                
                this.columns.push(data);
            }, this);
        },
        
        getColor: function (idx) {
            while (idx >= this.colors.length) {
                idx = idx - this.colors.length;
            }
            
            return this.colors[idx];
        }
    });
});
