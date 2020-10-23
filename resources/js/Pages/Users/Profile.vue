<template>
<div>
    <h1>Profile</h1>
    <inertia-link method="post" :href="`${$page.props.app.baseUrl}/logout`">logout</inertia-link>
</div>
</template>

<script>
export default {}
</script>
