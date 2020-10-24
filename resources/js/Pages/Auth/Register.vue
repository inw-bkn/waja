<template>
<div class="fixed inset-0 flex justify-center items-center bg-pattern">
    <div class="w-full max-w-xs md:max-w-lg bg-blue-200 p-4 md:p-8 rounded-md shadow-md relative">
        <h1 class="text-2xl font-semibold text-center text-gray-700">โปรดลงทะเบียน</h1>
        <div class="mt-4 md:mt-8">
            <label for="" class="tracking-wide text-xs truncate md:text-lg font-semibold mb-2">ชื่อ นามสกุล : </label>
            <input
                v-model="user.full_name"
                type="text"
                class="appearance-none w-full py-2 px-3 bg-gray-100 shadow-sm rounded border border-white
                        transition-all duration-200 ease-in-out
                        focus:outline-none focus:bg-white focus:border-gray-500
                        hover:border-gray-500 hover:bg-white
                        disabled:bg-gray-400">
        </div>

        <div class="mt-4 md:mt-8">
            <label for="" class="tracking-wide text-xs truncate md:text-lg font-semibold mb-2">ชื่อบัญชีผู้ใช้งาน : </label>
            <input
                v-model="user.name"
                type="text"
                class="appearance-none w-full py-2 px-3 bg-gray-100 shadow-sm rounded border border-white
                        transition-all duration-200 ease-in-out
                        focus:outline-none focus:bg-white focus:border-gray-500
                        hover:border-gray-500 hover:bg-white
                        disabled:bg-gray-400">
        </div>

        <div class="mt-4 md:mt-8" v-show="!user.email">
            <label for="" class="tracking-wide text-xs truncate md:text-lg font-semibold mb-2">อีเมล : </label>
            <input
                v-model="user.email"
                type="text"
                class="appearance-none w-full py-2 px-3 bg-gray-100 shadow-sm rounded border border-white
                        transition-all duration-200 ease-in-out
                        focus:outline-none focus:bg-white focus:border-gray-500
                        hover:border-gray-500 hover:bg-white
                        disabled:bg-gray-400">
        </div>

        <div class="mt-4 md:mt-8">
            <label for="" class="tracking-wide text-xs truncate md:text-lg font-semibold mb-2">หมายเลขโทรศัพท์ : </label>
            <input
                v-model="user.tel_no"
                type="text"
                class="appearance-none w-full py-2 px-3 bg-gray-100 shadow-sm rounded border border-white
                        transition-all duration-200 ease-in-out
                        focus:outline-none focus:bg-white focus:border-gray-500
                        hover:border-gray-500 hover:bg-white
                        disabled:bg-gray-400">
        </div>

        <div class="mt-4 md:mt-8 bg-red-400 text-gray-100 py-2 px-3 rounded" v-if="Object.keys($page.props.errors).length">
            <ul>
                <li v-for="key in Object.keys($page.props.errors)" :key="key" class="my-2">
                    • {{ $page.props.errors[key][0] }}
                </li>
            </ul>
        </div>

        <button
            class="w-full flex justify-center items-center btn btn-blue mt-8 md:mt-12"
            @click="register"
            :disabled="disabled || busy">
            <div v-if="busy" class="btn-spinner mr-2"></div>
            ลงทะเบียน
        </button>        
    </div>
</div>
</template>

<script>
export default {
props: ["social_profile"],
computed: {
    disabled () {
        if (this.user.tel_no === undefined || !this.user.tel_no) {
            return true
        }
        return !this.user.full_name || !this.user.name || !this.user.email || !this.user.tel_no
    }
},
data () {
    return {
        user: {},
        busy: false
    }
},
created () {
    this.user.full_name = this.social_profile.name
    this.user.name = this.social_profile.nickname
    this.user.email = this.social_profile.email
    this.user.id = this.social_profile.id
    this.user.provider = this.social_profile.provider
    this.user.avatar = this.social_profile.avatar
},
methods: {
    register () {
        this.busy = true
        this.$inertia.post(`${this.$page.props.app.baseUrl}/register`, this.user, {
            onSuccess: () => this.busy = false

        })
    }
}
}
</script>
