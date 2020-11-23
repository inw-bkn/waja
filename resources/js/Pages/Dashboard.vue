<template>
<div class="bg-pattern">
    <h1>Dashboard</h1>
    <inertia-link method="post" :href="`${$page.props.app.baseUrl}/logout`">logout</inertia-link>
</div>
</template>

<script>
export default {
props: ["requestForms"]
}
</script>
