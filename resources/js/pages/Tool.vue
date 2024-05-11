<template>
    <div>
        <Head :title="report_name"/>

        <div
            id="jshxl_report_data_ease"
            style="width: 100%;height: calc(100vh - 176px);"
        >
        </div>
    </div>
</template>

<script>
import {dataease} from '../dataease';

export default {
    props: ['report_name', 'dvId', 'chartId', 'DEDomain'],

    mounted() {
        this.initLoad();
    },

    methods: {
        initLoad() {
            if (document.querySelector('dataease') === null) {
                console.log('第一次加载，开始加载数据大屏前端资源');
                this.loadAssets();
            } else {
                console.log('历史已经加载过数据大屏前端资源');
                this.loadDashboard();
            }
        },

        loadDashboard() {
            const config = {
                baseUrl: this.DEDomain + '/',            // DataEase 访问信息
                token: this.sign,                        //生成的用户 token
                dvId: this.dvId,                         //仪表板 ID
                busiFlag: "dashboard"                    //固定值:仪表板为 dashboard，数据大屏为 dataV
            };
            if (this.chartId !== null) config.chartId = this.chartId;
            const dataease = new DataEaseBi("Dashboard", config);
            dataease.initialize({container: '#jshxl_report_data_ease'})
        },

        loadAssets() {
            const eleArr = dataease.getAssets();
            const preUrl = this.DEDomain;

            let eleAtt = document.createElement('dataease');
            let finish = eleArr.length;
            let that = this;

            function drawDataEase() {
                if (finish !== 0) return;
                console.log('所有数据大屏前端资源加载完毕，现在开始渲染图表');
                that.loadDashboard();
            }

            function produceTag(obj, name) {
                let element = document.createElement(name);
                Object.entries(obj).forEach(([key, value]) => {


                    if (['href', 'src'].includes(key)) {
                        const relativeVal = value.startsWith('./') ? value.substr(1) : value;
                        element[key] = preUrl + `${relativeVal}`;
                    } else {
                        element.setAttribute(key, value || '');
                    }
                });
                element.setAttribute('crossorigin', '');
                element.onload = function () {
                    finish--;
                    drawDataEase();
                };
                eleAtt.appendChild(element);
            }

            eleArr.forEach((ele) => {
                produceTag(ele.attributes, ele.name);
            });
            document.documentElement.insertBefore(eleAtt, document.querySelector('head'));
        }
    }
}
</script>
