<template>
<div>
    <h1 class="bg-red-200 text-purple-600">I am the page</h1>
    <hello />
    <inertia-link href="/login">login</inertia-link>
    <inertia-link href="/about">about</inertia-link>
</div>
</template>

<script>
import Hello from "@/Components/Hello"
export default {
components: { Hello }
}
</script>
