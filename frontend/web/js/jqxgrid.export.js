/*
jQWidgets v5.5.0 (2017-Dec)
Copyright (c) 2011-2017 jQWidgets.
License: https://jqwidgets.com/license/
*/
(function ($) {
    $.extend($.jqx._jqxGrid.prototype, {
        exportdata: function (datatype, filename, exportHeader, rows, exportHiddenColumns, exportServer, charset) {
            if (!$.jqx.dataAdapter.ArrayExporter) {
                throw 'jqxGrid: Missing reference to jqxdata.export.js!';
            }

            if (exportHeader == undefined) {
                exportHeader = true;
            }

            var me = this;

            if (rows == undefined) {
                var rows = this.getrows();
                if (rows.length == 0) {
                    throw 'No data to export.';
                }
            }

            this.exporting = true;
            if (!this.pageable) {
                this.loadondemand = true;
            }

            if (this.altrows) {
                this._renderrows(this.virtualsizeinfo);
            }

            var addhiddencolumns = exportHiddenColumns != undefined ? exportHiddenColumns : false;
            var dataFields = {};
            var styles = {};
            var alignments = [];
            var $cell = this.host.find('.jqx-grid-cell:first');
            var $cellalt = this.host.find('.jqx-grid-cell-alt:first');
            $cell.removeClass(this.toThemeProperty('jqx-grid-cell-selected'));
            $cell.removeClass(this.toThemeProperty('jqx-fill-state-pressed'));
            $cellalt.removeClass(this.toThemeProperty('jqx-grid-cell-selected'));
            $cellalt.removeClass(this.toThemeProperty('jqx-fill-state-pressed'));
            $cell.removeClass(this.toThemeProperty('jqx-grid-cell-hover'));
            $cell.removeClass(this.toThemeProperty('jqx-fill-state-hover'));
            $cellalt.removeClass(this.toThemeProperty('jqx-grid-cell-hover'));
            $cellalt.removeClass(this.toThemeProperty('jqx-fill-state-hover'));

            var styleName = 'cell';
            var styleIndex = 1;
            var columnStyleName = 'column';
            var columnStyleIndex = 1;
            var aggregates = [];

            for (var j = 0; j < this.columns.records.length; j++) {
                var column = this.columns.records[j];
                if (column.cellclassname != "") {
                    column.customCellStyles = new Array();
                    if (typeof column.cellclassname == "string") {
                        for (var i = 0; i < rows.length; i++) {
                            column.customCellStyles[i] = column.cellclassname;
                        }
                    }
                    else {
                        for (var i = 0; i < rows.length; i++) {
                            var boundIndex = this.getrowboundindex(i);
                            var className = column.cellclassname(boundIndex, column.displayfield, rows[i][column.displayfield], rows[i]);
                            if (className) {
                                column.customCellStyles[i] = className;
                            }
                        }
                    }
                }
            }

            var fields = new Array();
            var firstColumn = null;
            var firstCell = null;
            var firstAltCell = null;
            $.each(this.columns.records, function (index)
            {
                var $cell = $(me.table[0].rows[0].cells[index]);
                if (me.table[0].rows.length > 1) {
                    var $cellalt = $(me.table[0].rows[1].cells[index]);
                    if (!firstAltCell)
                        firstAltCell = $cellalt;
                }
                if (!firstCell) firstCell = $cell;

                var column = this;
                var removeClassFunc = function (cell) {
                    cell.removeClass(me.toThemeProperty('jqx-grid-cell-selected'));
                    cell.removeClass(me.toThemeProperty('jqx-fill-state-pressed'));
                    cell.removeClass(me.toThemeProperty('jqx-grid-cell-hover'));
                    cell.removeClass(me.toThemeProperty('jqx-fill-state-hover'));
                    if (column.customCellStyles) {
                        for (var o in column.customCellStyles) {
                            cell.removeClass(column.customCellStyles[o]);
                        }
                    }
                }
                removeClassFunc($cell);
                if ($cellalt) {
                    removeClassFunc($cellalt);
                }

                if (this.displayfield == null) return true;

                if (me.showaggregates) {
                    if (me.getcolumnaggregateddata) {
                        aggregates.push(me.getcolumnaggregateddata(this.displayfield, this.aggregates, true, rows));
                    }
                }

                var type = me._getexportcolumntype(this);
                if (this.exportable && (!this.hidden || addhiddencolumns)) {
                    dataFields[this.displayfield] = {};
                    dataFields[this.displayfield].text = this.text;
                    dataFields[this.displayfield].width = parseInt(this.width);
                    if (isNaN(dataFields[this.displayfield].width)) dataFields[this.displayfield].width = 60;
                    dataFields[this.displayfield].formatString = this.cellsformat;
                    dataFields[this.displayfield].localization = me.gridlocalization;
                    dataFields[this.displayfield].type = type;
                    dataFields[this.displayfield].cellsAlign = this.cellsalign;
                    dataFields[this.displayfield].hidden = !exportHeader;
                    dataFields[this.displayfield].displayfield = this.displayfield;
                    fields.push(dataFields[this.displayfield]);
                }

                styleName = 'cell' + styleIndex;

                var $element = $(this.element);
                if (this.element == undefined) $element = $(this.uielement);
                if (!firstColumn)
                {
                    firstColumn = $element;
                }
                else if (!column._rendered)
                {
                    $element = firstColumn;
                    $cell = firstCell;
                    $cellalt = firstAltCell;
                    var cellclass = me.toTP('jqx-grid-cell') + ' ' + me.toTP('jqx-item');
                    $cell[0].className = cellclass;
                    cellclass += me.toTP('jqx-grid-cell-alt');
                    if ($cellalt) {
                        $cellalt[0].className = cellclass;
                    }
                }

                columnStyleName = 'column' + columnStyleIndex;
                if (datatype == 'html' || datatype == 'xls' || datatype == 'pdf') {
                    var buildStyle = function (styleName, $element, isColumn, altStyle, meColumn, me, index, customStyle, rowIndex) {
                        styles[styleName] = {};
                        if ($element == undefined)
                            return;

                        styles[styleName]['font-size'] = $element.css('font-size');
                        styles[styleName]['font-weight'] = $element.css('font-weight');
                        styles[styleName]['font-style'] = $element.css('font-style');
                        styles[styleName]['background-color'] = me._getexportcolor($element.css('background-color'));
                        styles[styleName]['color'] = me._getexportcolor($element.css('color'));
                        styles[styleName]['border-color'] = me._getexportcolor($element.css('border-top-color'));
                        if (isColumn) {
                            styles[styleName]['text-align'] = meColumn.align;
                        }
                        else {
                            styles[styleName]['text-align'] = meColumn.cellsalign;
                            styles[styleName]['formatString'] = meColumn.cellsformat;
                            styles[styleName]['dataType'] = type;
                        }

                        if (datatype == 'html' || datatype == 'pdf') {
                            styles[styleName]['border-top-width'] = $element.css('border-top-width');
                            styles[styleName]['border-left-width'] = $element.css('border-left-width');
                            styles[styleName]['border-right-width'] = $element.css('border-right-width');
                            styles[styleName]['border-bottom-width'] = $element.css('border-bottom-width');
                            styles[styleName]['border-top-style'] = $element.css('border-top-style');
                            styles[styleName]['border-left-style'] = $element.css('border-left-style');
                            styles[styleName]['border-right-style'] = $element.css('border-right-style');
                            styles[styleName]['border-bottom-style'] = $element.css('border-bottom-style');
                            if (isColumn) {
                                if (index == 0) {
                                    styles[styleName]['border-left-width'] = $element.css('border-right-width');
                                }
                                styles[styleName]['border-top-width'] = $element.css('border-right-width');
                                styles[styleName]['border-bottom-width'] = $element.css('border-bottom-width');
                            }
                            else {
                                if (index == 0) {
                                    styles[styleName]['border-left-width'] = $element.css('border-right-width');
                                }
                            }
                            styles[styleName]['height'] = $element.css('height');
                        }

                        if (meColumn.exportable && (!meColumn.hidden || addhiddencolumns)) {
                            if (customStyle == true) {
                                if (!dataFields[meColumn.displayfield].customCellStyles) {
                                    dataFields[meColumn.displayfield].customCellStyles = new Array();
                                }

                                dataFields[meColumn.displayfield].customCellStyles[rowIndex] = styleName;
                            }
                            else {
                                if (isColumn) {
                                    dataFields[meColumn.displayfield].style = styleName;
                                }
                                else if (!altStyle) {
                                    dataFields[meColumn.displayfield].cellStyle = styleName;
                                }
                                else dataFields[meColumn.displayfield].cellAltStyle = styleName;
                            }
                        }
                    }
                    buildStyle(columnStyleName, $element, true, false, this, me, index);
                    columnStyleIndex++;
                    buildStyle(styleName, $cell, false, false, this, me, index);
                    if (me.altrows) {
                        styleName = 'cellalt' + styleIndex;
                        buildStyle(styleName, $cellalt, false, true, this, me, index);
                    }
                    if (this.customCellStyles) {
                        for (var o in column.customCellStyles) {
                            $cell.removeClass(column.customCellStyles[o]);
                        }
                        for (var o in column.customCellStyles) {
                            $cell.addClass(column.customCellStyles[o]);
                            buildStyle(styleName + column.customCellStyles[o], $cell, false, false, this, me, index, true, o);
                            $cell.removeClass(column.customCellStyles[o]);
                        }
                    }

                    styleIndex++;
                }
            });
            $.each(this.columns.records, function (index) {
                if (dataFields[this.displayfield]) {
                    dataFields[this.displayfield].columnsDataFields = fields;
                }
            });

            if (this.showaggregates) {
                var aggregatedrows = [];
                var prefix = datatype == 'xls' ? "_AG" : "";
                var offset = this.groupable ? this.groups.length : 0;
                if (this.rowdetails) offset++;
                if (this.selectionmode === "checkbox") offset++;
                if (aggregates.length > 0) {
                    $.each(this.columns.records, function (index) {
                        if (this.aggregates) {
                            for (var i = 0; i < this.aggregates.length; i++) {
                                if (!aggregatedrows[i]) aggregatedrows[i] = {};
                                if (aggregatedrows[i]) {
                                    var aggregatename = me._getaggregatename(this.aggregates[i]);
                                    var aggregatetype = me._getaggregatetype(this.aggregates[i]);
                                    var aggregate = aggregates[index-offset];
                                    if (aggregate) {
                                        aggregatedrows[i][this.displayfield] = prefix + aggregatename + ": " + aggregate[aggregatetype];
                                    }
                                }
                            }
                        }
                    });
                    $.each(this.columns.records, function (index) {
                        for (var i = 0; i < aggregatedrows.length; i++) {
                            if (aggregatedrows[i][this.displayfield] == undefined) {
                                aggregatedrows[i][this.displayfield] = prefix;
                            }
                        }
                    });
                }
                $.each(aggregatedrows, function () {
                    rows.push(this);
                });
            }

            var that = this;
            var exporter = $.jqx.dataAdapter.ArrayExporter(rows, dataFields, styles);
            if (filename == undefined) {
                // update ui
                this._renderrows(this.virtualsizeinfo);
                var result = exporter.exportTo(datatype);
                if (this.showaggregates) {
                    $.each(aggregatedrows, function () {
                        rows.pop(this);
                    });
                }
                
                setTimeout(function () {
                    that.exporting = false;
                }, 50);
                return result;
            }
            else {
                exporter.exportToFile(datatype, filename, exportServer, charset);
            }
            // update ui
            if (this.showaggregates) {
                $.each(aggregatedrows, function () {
                    rows.pop(this);
                });
            }
            this._renderrows(this.virtualsizeinfo);
            setTimeout(function () {
                that.exporting = false;
            }, 50);
        },

        _getexportcolor: function (value) {
            var color = value;
            if (value == 'transparent') color = "#FFFFFF";
            if (!color || !color.toString()) {
                color = "#FFFFFF";
            }

            if (color.toString().indexOf('rgb') != -1) {
                var rgb = color.split(',');
                if (color.toString().indexOf('rgba') != -1) {
                    var r = parseInt(rgb[0].substring(5));
                    var g = parseInt(rgb[1]);
                    var b = parseInt(rgb[2]);
                    var a = parseInt(rgb[3].substring(1, 4));
                    var rgbObj = { r: r, g: g, b: b };
                    var hex = this._rgbToHex(rgbObj);
                    if (r == 0 && g == 0 && b == 0 && a == 0) {
                        return "#ffffff";
                    }

                    return "#" + hex;
                }

                var r = parseInt(rgb[0].substring(4));
                var g = parseInt(rgb[1]);
                var b = parseInt(rgb[2].substring(1, 4));
                var rgbObj = { r: r, g: g, b: b };
                var hex = this._rgbToHex(rgbObj);
                return "#" + hex;
            }
            else if (color.toString().indexOf('#') != -1) {
                if (color.toString().length == 4) {
                    var colorPart = color.toString().substring(1, 4);
                    color += colorPart;
                }
            }

            return color;
        },

        _rgbToHex: function (rgb) {
            return this._intToHex(rgb.r) + this._intToHex(rgb.g) + this._intToHex(rgb.b);
        },

        _intToHex: function (dec) {
            var result = (parseInt(dec).toString(16));
            if (result.length == 1)
                result = ("0" + result);
            return result.toUpperCase();
        },

        _getexportcolumntype: function (column) {
            var me = this;
            var type = 'string';
            var datafields = me.source.datafields || ((me.source._source) ? me.source._source.datafields : null);

            if (datafields) {
                var foundType = "";
                $.each(datafields, function () {
                    if (this.name == column.displayfield) {
                        if (this.type) {
                            foundType = this.type;
                        }
                        return false;
                    }
                });
                if (foundType)
                    return foundType;
            }

            if (column != null) {
                if (this.dataview.cachedrecords == undefined) {
                    return type;
                }

                var cell = null;

                if (!this.virtualmode) {
                    if (this.dataview.cachedrecords.length == 0)
                        return type;

                    cell = this.dataview.cachedrecords[0][column.displayfield];
                    if (cell != null && cell.toString() == "") {
                        return "string";
                    }
                }
                else {
                    $.each(this.dataview.cachedrecords, function () {
                        cell = this[column.displayfield];
                        return false;
                    });
                }

                if (cell != null) {
                    if (column.cellsformat.indexOf('c') != -1) {
                        return 'number';
                    }
                    if (column.cellsformat.indexOf('n') != -1) {
                        return 'number';
                    }
                    if (column.cellsformat.indexOf('p') != -1) {
                        return 'number';
                    }
                    if (column.cellsformat.indexOf('d') != -1) {
                        return 'date';
                    }
                    if (column.cellsformat.indexOf('y') != -1) {
                        return 'date';
                    }
                    if (column.cellsformat.indexOf('M') != -1) {
                        return 'date';
                    }
                    if (column.cellsformat.indexOf('m') != -1) {
                        return 'date';
                    }
                    if (column.cellsformat.indexOf('t') != -1) {
                        return 'date';
                    }

                    if (typeof cell == 'boolean') {
                        type = 'boolean';
                    }
                    else if ($.jqx.dataFormat.isNumber(cell)) {
                        type = 'number';
                    }
                    else {
                        var tmpvalue = new Date(cell);
                        if (tmpvalue.toString() == 'NaN' || tmpvalue.toString() == "Invalid Date") {
                            if ($.jqx.dataFormat) {
                                tmpvalue = $.jqx.dataFormat.tryparsedate(cell);
                                if (tmpvalue != null) {
                                    if (tmpvalue && tmpvalue.getFullYear()) {
                                        if (tmpvalue.getFullYear() == 1970 && tmpvalue.getMonth() == 0 && tmpvalue.getDate() == 1) {
                                            var num = new Number(cell);
                                            if (!isNaN(num))
                                                return 'number';

                                            return 'string';
                                        }
                                    }

                                    return 'date';
                                }
                                else {
                                    type = 'string';
                                }
                            }
                            else type = 'string';
                        }
                        else {
                            type = 'date';
                        }
                    }
                }
            }
            return type;
        }

    });
})(jqxBaseFramework);


