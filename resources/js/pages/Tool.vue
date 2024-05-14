<template>
    <div>
        <Head :title="report"/>

        <iframe
            id="jshxl_report_data_ease"
            style="width: 100%;min-height: calc(100vh - 176px);"
        ></iframe>
    </div>
</template>

<script>
import {dataease} from '../dataease';

export default {
    props: ['report', 'token', 'deType', 'deId', 'chartId', 'DEDomain'],

    mounted() {
        // DIV嵌入方式
        // this.initLoad();

        // IFRAME嵌入方式
        this.loadReport();
    },

    methods: {
        // 通过iframe加载数据报表
        loadReport() {
            const iframe = document.getElementById('jshxl_report_data_ease');
            const domain = this.DEDomain + '/#/chart-view';
            iframe.src = domain;
            const params = {
                type: 'Dashboard',
                embeddedToken: this.token,                          // 生成的用户 token
                dvId: this.deId,                                    // 仪表板或数据大屏 ID
                busiFlag: this.deType                               // 固定值:仪表板为 dashboard，数据大屏为 dataV
            };
            window.addEventListener('message', event => {
                if (event.data?.msgOrigin !== 'de-fit2cloud') return;
                params['de-embedded'] = true;
                iframe.contentWindow.postMessage(params, domain);
            });
        },

        // 加载需要的前端资源文件或加载图表
        initLoad() {
            if (document.querySelector('dataease') === null) {
                console.log('第一次加载，开始加载数据大屏前端资源');
                this.loadAssets();
            } else {
                console.log('历史已经加载过数据大屏前端资源');
                this.loadDashboard();
            }
        },

        // 加载数据报表
        loadDashboard() {
            const config = {
                baseUrl: this.DEDomain + '/',            // DataEase 访问信息
                token: this.token,                       // 生成的用户 token
                dvId: this.deId,                         // 仪表板或数据大屏 ID
                busiFlag: this.deType                    // 固定值:仪表板为 dashboard，数据大屏为 dataV
            };
            if (typeof this.chartId === 'string' && this.chartId !== '')
                config.chartId = this.chartId;
            const dataease = new DataEaseBi('Dashboard', config);
            dataease.initialize({container: '#jshxl_report_data_ease'})
        },

        // 加载前端资源文件
        loadAssets() {
            const preUrl = this.DEDomain;

            // 写入一个无效的标签到header头，方便DE前端读取资源的域名
            let domain = document.createElement('script');
            domain.setAttribute('href', preUrl + '/js/div_import_0.0.0-dataease.js');
            document.head.appendChild(domain);

            // 手动加载DE前端资源文件（CSS + 一个JS文件）
            const eleArr = dataease.getAssets();
            let finish = eleArr.length;
            let eleAtt = document.createElement('dataease');
            let parObj = this;

            function drawDataEase() {
                if (finish !== 0) return;
                console.log('所有数据大屏前端资源加载完毕，现在开始渲染图表');
                parObj.loadDashboard();
            }

            function produceTag(obj, name) {
                let element = document.createElement(name);
                Object.entries(obj).forEach(([key, value]) => {
                    if (['href', 'src'].includes(key)) {
                        element[key] = preUrl + (value.startsWith('./') ? value.substring(1) : value);
                    } else {
                        element.setAttribute(key, typeof value === 'string' ? value : '');
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
