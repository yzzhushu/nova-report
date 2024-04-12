<template>
    <div>
        <Head :title="viewTitle"/>

        <Heading class="mb-6">{{reportName === '' ? '加载中...' : reportName}}</Heading>

        <DataTable
            :value="lists"
            scrollable
            :scrollHeight="scrollHeight"
            :virtualScrollerOptions="{itemSize: 40}"
            selectionMode="single"
            @rowSelect="$emit('row:selected', $event)"
            removableSort
            :pt="{
            root: 'mt-1 mb-1 relative',
            headerrow: 'h-10',
            bodyrow: 'h-10'
        }"
        >
            <Column
                v-for="column of columns"
                :key="column.field"
                :field="column.field"
                :header="column.header"
                :sortable="column.sort"
                :style="'width: ' + (column.width ? column.width : (100 / columns.length)) + '%'"
            />
        </DataTable>
    </div>
</template>

<script>
export default {
    props: ['reportId'],

    data() {
        return {
            viewTitle: '宏信报表',
            reportName: '',
            columns: [],
            lists: [],
            scrollHeight: '360px',
        };
    },

    mounted() {
        this.loadStructure();
    },

    methods: {
        loadStructure() {
            Nova.request().post('/nova-vendor/report/structure', {
                reportId: this.reportId
            }).then(response => {
                if (response.data.error !== undefined) {
                    this.viewTitle = '发生错误';
                    this.reportName = response.data.error;
                    return;
                }
                this.reportName = response.data.name;
                this.columns = response.data.fields;
            });
        }
    }
}
</script>
