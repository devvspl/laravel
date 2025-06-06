function getChartColorsArray(e) {
    if (null !== document.getElementById(e)) {
        var t = "data-colors" + ("-" + document.documentElement.getAttribute("data-theme") ?? ""),
            t = document.getElementById(e).getAttribute(t) ?? document.getElementById(e).getAttribute("data-colors");
        if (t)
            return (t = JSON.parse(t)).map(function (e) {
                var t = e.replace(" ", "");
                return -1 === t.indexOf(",")
                    ? getComputedStyle(document.documentElement).getPropertyValue(t) || t
                    : 2 == (e = e.split(",")).length
                    ? "rgba(" + getComputedStyle(document.documentElement).getPropertyValue(e[0]) + "," + e[1] + ")"
                    : t;
            });
        console.warn("data-colors attributes not found on", e);
    }
}
var options,
    chart,
    linechartcustomerColors = getChartColorsArray("projects-overview-chart"),
    isApexSeriesData =
        (linechartcustomerColors &&
            ((options = {
                series: [
                    { name: "Number of Projects", type: "bar", data: [34, 65, 46, 68, 49, 61, 42, 44, 78, 52, 63, 67] },
                    { name: "Revenue", type: "area", data: [89.25, 98.58, 68.74, 108.87, 77.54, 84.03, 51.24, 28.57, 92.57, 42.36, 88.51, 36.57] },
                    { name: "Active Projects", type: "bar", data: [8, 12, 7, 17, 21, 11, 5, 9, 7, 29, 12, 35] },
                ],
                chart: { height: 374, type: "line", toolbar: { show: !1 } },
                stroke: { curve: "smooth", dashArray: [0, 3, 0], width: [0, 1, 0] },
                fill: { opacity: [1, 0.1, 1] },
                markers: { size: [0, 4, 0], strokeWidth: 2, hover: { size: 4 } },
                xaxis: { categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"], axisTicks: { show: !1 }, axisBorder: { show: !1 } },
                grid: { show: !0, xaxis: { lines: { show: !0 } }, yaxis: { lines: { show: !1 } }, padding: { top: 0, right: -2, bottom: 15, left: 10 } },
                legend: { show: !0, horizontalAlign: "center", offsetX: 0, offsetY: -5, markers: { width: 9, height: 9, radius: 6 }, itemMargin: { horizontal: 10, vertical: 0 } },
                plotOptions: { bar: { columnWidth: "30%", barHeight: "70%" } },
                colors: linechartcustomerColors,
                tooltip: {
                    shared: !0,
                    y: [
                        {
                            formatter: function (e) {
                                return void 0 !== e ? e.toFixed(0) : e;
                            },
                        },
                        {
                            formatter: function (e) {
                                return void 0 !== e ? "$" + e.toFixed(2) + "k" : e;
                            },
                        },
                        {
                            formatter: function (e) {
                                return void 0 !== e ? e.toFixed(0) : e;
                            },
                        },
                    ],
                },
            }),
            (chart = new ApexCharts(document.querySelector("#projects-overview-chart"), options)).render()),
        {}),
    isApexSeries = document.querySelectorAll("[data-chart-series]"),
    donutchartProjectsStatusColors =
        (isApexSeries &&
            Array.from(isApexSeries).forEach(function (e) {
                var t,
                    e = e.attributes;
                e["data-chart-series"] &&
                    ((isApexSeriesData.series = e["data-chart-series"].value.toString()),
                    (t = getChartColorsArray(e.id.value.toString())),
                    (t = {
                        series: [isApexSeriesData.series],
                        chart: { type: "radialBar", width: 36, height: 36, sparkline: { enabled: !0 } },
                        dataLabels: { enabled: !1 },
                        plotOptions: { radialBar: { hollow: { margin: 0, size: "50%" }, track: { margin: 1 }, dataLabels: { show: !1 } } },
                        colors: t,
                    }),
                    new ApexCharts(document.querySelector("#" + e.id.value.toString()), t).render());
            }),
        getChartColorsArray("prjects-status")),
    currentChatId =
        (donutchartProjectsStatusColors &&
            ((options = {
                series: [125, 42, 58, 89],
                labels: ["Completed", "In Progress", "Yet to Start", "Cancelled"],
                chart: { type: "donut", height: 230 },
                plotOptions: { pie: { size: 100, offsetX: 0, offsetY: 0, donut: { size: "90%", labels: { show: !1 } } } },
                dataLabels: { enabled: !1 },
                legend: { show: !1 },
                stroke: { lineCap: "round", width: 0 },
                colors: donutchartProjectsStatusColors,
            }),
            (chart = new ApexCharts(document.querySelector("#prjects-status"), options)).render()),
        "users-chat");
function scrollToBottom(e) {
    setTimeout(() => {
        new SimpleBar(document.getElementById("chat-conversation")).getScrollElement().scrollTop = document.getElementById("users-conversation").scrollHeight;
    }, 100);
}
scrollToBottom(currentChatId);
