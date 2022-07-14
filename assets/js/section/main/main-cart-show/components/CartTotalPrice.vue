<template>
    <div class="mb-2">
        <span>
            Total: <b>${{totalPrice}}</b>
        </span>
    </div>
</template>

<script>
	import {mapGetters} from "vuex";

	export default {
		name: "CartTotalPrice",
		computed: {
			...mapGetters('cart', ['totalPrice'])
		}
	}
</script>

<style scoped>

</style>