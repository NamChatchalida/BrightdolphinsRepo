# Elementor Addon Components
**Elementor Chart** is a new widget that will allow you to produce, publish amazing and beautiful interactive charts.  
You can create **seven most** used chart types with tons of customization options.  
You will be able to apply different style settings and customize widget according to your data visualization.

## WordPress Plugin
> **Author:**  **EAC Team**
* **Tags**: elementor,component,widget,chart
* **Requires Elementor** at least: 2.0
* **Wordpress tested up to**: 4.8
* **Requires PHP**: 5.2.4
* **Stable tag**: 1.5.4
* **License**: GPLv3 or later
* **License URI**: https://www.gnu.org/licenses/gpl-3.0.html

## Chart type
* Bar, Horizontal Bar, Line, Pie, Doughnut, Radar, Polar Area

## Library and Plugins
Lib. [ChartJS](https://www.chartjs.org/).
Plugins: [Datalabels](https://chartjs-plugin-datalabels.netlify.app/).  [Style](https://github.com/nagix/chartjs-plugin-style) 

## Format JSON File
Number JSON files are available in the **assets/js/chart/json** directory.
> **Keys/values** must be surrounded by double quotes
 **Comma** is not allowed at the end of the object
 **labels, label, data** are mandatory
 **data** use comma to separate values
#
|{|            |              |                |                             |                          |
|-|------------|--------------|----------------|-----------------------------|--------------------------|
| |"title":    |"Chart title",|
| |"labels":   |"",           |**Mandatory**   |
| |"datasets": |
| |[{          |
| |            |"label":      |"Serie label 1",|
| |            |"data":       |"1,2,3,4,5"     |**Mandatory**                |
| |        }, {|
| |            |"label":      |"Serie label 2",|**Mandatory**                |
| |            |"data":       |""              |**No comma**                 |
| |}],         |              |
| |"options":  |{             |
| |            |"stacked":    |"0",            |**"1": To stack Bars, Lines**|
| |            |"stepped":    |"0",            |**"1": Line**                |
| |            |"x_axis":     |{               |
| |            |              |"title":        |"X Axis title"               |
| |            |},            |
| |            |"y_axis":     |{               |
| |            |              |"title":        |"Y Axis title",              |
| |            |              |"suffix":       |""                           |**Suffix to Y data**      |
| |            |},            |
| |            |"y_axis2":    |{               |
| |            |              |"display":      |"0",                         |**"1" display right axis**|
| |            |              |"title":        |"Y2 Axis title",             |
| |            |              |"suffix":       |""                           |**Suffix to Y2 data**     |
| |            |}             |**No comma**    |
| |}           |
|}|

***
{
    "title": "Chart title",
    "labels": "labels", **Mandatory**
    "datasets":
    [{
            "label": "Serie label 1", **Mandatory**
            "data": "1,2,3,4,5" **Mandatory**
        }, {
            "label": "Serie label 2",
            "data": "" **No comma**
    }],
    
    "options": {
        "stacked": "0", **"1": To stack Bars, Lines**
        "stepped": "0", **"1": Line**
        "x_axis": {
            "title": "X Axis title"
        },
        "y_axis": {
            "title": "Y Axis title",
            "suffix": "" **Suffix to Y data**
        },
        "y_axis2": {
            "display": "0", **"1" display right axis**
            "title": "Y Axis 2 title",
            "suffix": "" **Suffix to Y2 data**
        }
    }
}
