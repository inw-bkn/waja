<template>
<div class="fixed inset-0 flex justify-center items-center bg-pattern">
    <div class="w-full max-w-xs md:max-w-lg bg-blue-200 p-4 md:p-8 rounded-md shadow-md relative">
        <template v-if="latestForm && latestForm.status == 'pending'">
            <h1 class="text-2xl font-semibold text-center text-gray-700">YOUR REQUEST IS PENDING</h1>
            <div class="mt-4 md:mt-8 p-2 text-sm text-green-400 text-center">
                PLEASE CHECK AGAIN LATER
            </div>
            <inertia-link
                class="w-full flex justify-center items-center btn btn-blue mt-8 md:mt-12"
                :href="`${$page.props.app.baseUrl}/profile`">
                GOT IT
            </inertia-link>
        </template>
        <template v-else>
            <h1 class="text-2xl font-semibold text-center text-gray-700">DEVELOPER REQUEST FORM</h1>
            <div class="mt-4 md:mt-8 p-2 text-sm text-gray-200 bg-green-400 shadow-sm rounded">
                With a developer role, you can use WAJA as an authentication service for your apps.
            </div>
            <div class="mt-4 md:mt-8">
                <label for="" class="tracking-wide text-xs truncate md:text-lg font-semibold mb-2">Tell us about your application : </label>
                <textarea
                    v-model="form.detail"
                    type="text"
                    rows="6"
                    class="appearance-none w-full py-2 px-3 bg-gray-100 shadow-sm rounded border border-white
                            transition-all duration-200 ease-in-out
                            focus:outline-none focus:bg-white focus:border-gray-500
                            hover:border-gray-500 hover:bg-white
                            disabled:bg-gray-400"></textarea>
            </div>
            <button
                class="w-full flex justify-center items-center btn btn-blue mt-8 md:mt-12"
                @click="submit"
                :disabled="!form.detail || busy">
                <div v-if="busy" class="btn-spinner mr-2"></div>
                SUBMIT
            </button>
        </template>
    </div>
</div>
</template>

<script>
export default {
props: ["latestForm"],
data () {
    return {
        form: {
            detail: null
        },
        busy: false
    }
},
methods: {
    submit () {
        this.busy = true
        this.$inertia.post(`${this.$page.props.app.baseUrl}/developer`, this.form, {
            onSuccess: page => {
                console.log(page)
                this.busy = false
            }
        })
    }
}
}
</script>
