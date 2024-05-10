<template>
    <div>
        <Head :title="report_name"/>

        <iframe :src="data_ease" style="width: 100%;min-height: 360px;"></iframe>
    </div>
</template>

<script>
export default {
    props: ['report_name', 'data_ease']
}
</script>
