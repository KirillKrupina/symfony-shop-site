import {concatUrlByParams} from "../../../../../utils/urlGenerator";
import axios from "axios";
import {StatusCodes} from "http-status-codes";
import {apiConfig} from "../../../../../utils/settings";

const state = () => ({
	categories: [],
	productsByCategory: [],
	orderProducts: [],
	busyProductsIds: [],
	isLoaded: false,
	newOrderProduct: {
		categoryId: '',
		productId: '',
		quantity: '',
		pricePerOne: ''
	},
	staticStore: {
		orderId: window.staticStore.orderId,
		url: {
			viewProduct: window.staticStore.urlViewProduct,
			apiOrderProduct: window.staticStore.urlApiOrderProduct,
			apiCategories: window.staticStore.urlApiCategories,
			apiProducts: window.staticStore.urlApiProducts,
			apiOrders: window.staticStore.urlApiOrders
		}
	}
});

const getters = {
	freeProductsByCategory(state) {
		return state.productsByCategory.filter(
			item => state.busyProductsIds.indexOf(item.id) === -1
		);
	}
};

const actions = {
	async getOrderProducts({commit, state}) {
		commit('setIsLoadedFalse');
		const url = concatUrlByParams(state.staticStore.url.apiOrders, state.staticStore.orderId);
		const result = await axios.get(url, apiConfig);
		if (result.data && result.status === StatusCodes.OK) {
			commit('setOrderProducts', result.data.orderProducts);
			commit('setIsLoadedTrue');
			commit('setBusyProductsIds');
		}
	},
	async getProductsByCategory({commit, state}) {
		const url = state.staticStore.url.apiProducts
			+ '?category=' + state.newOrderProduct.categoryId
			+ '&isPublished=true'
			+ '&page=1&itemsPerPage=30';

		const result = await axios.get(url, apiConfig);
		if (result.data && result.status === StatusCodes.OK) {
			commit('setProductsByCategory', result.data['hydra:member']);
		}
	},
	async getCategories({commit, state}) {
		const url = state.staticStore.url.apiCategories;
		const result = await axios.get(url, apiConfig);
		if (result.data && result.status === StatusCodes.OK) {
			commit('setCategories', result.data['hydra:member']);
		}
	},
	async addNewOrderProduct({state, dispatch}) {
		const url = state.staticStore.url.apiOrderProduct;
		const data = {
			pricePerOne: state.newOrderProduct.pricePerOne,
			quantity: parseInt(state.newOrderProduct.quantity),
			product: '/api/products/' + state.newOrderProduct.productId,
			appOrder: '/api/orders/' + state.staticStore.orderId
		};

		const result = await axios.post(url, data, apiConfig);
		if (result.data && result.status === StatusCodes.CREATED) {
			dispatch('getOrderProducts');
		}
	},
	async removeOrderProduct({state, dispatch}, orderProductId) {
		const url = concatUrlByParams(state.staticStore.url.apiOrderProduct, orderProductId);
		const result = await axios.delete(url, apiConfig);
		if (result.status === StatusCodes.NO_CONTENT) {
			dispatch('getOrderProducts');
		}
	}
};

// need for actions with state
const mutations = {
	setCategories(state, categories) {
		state.categories = categories;
	},
	setOrderProducts(state, orderProducts) {
		state.orderProducts = orderProducts;
	},
	setProductsByCategory(state, products) {
		state.productsByCategory = products;
	},
	setNewProductInfo(state, formData) {
		state.newOrderProduct.categoryId = formData.categoryId;
		state.newOrderProduct.productId = formData.productId;
		state.newOrderProduct.quantity = formData.quantity;
		state.newOrderProduct.pricePerOne = formData.pricePerOne;
	},
	setBusyProductsIds(state) {
		state.busyProductsIds = state.orderProducts.map(item => item.product.id);
	},
	setIsLoadedFalse(state) {
		state.isLoaded = false;
	},
	setIsLoadedTrue(state) {
		state.isLoaded = true;
	}
};

export default {
	namespaced: true,
	state,
	getters,
	actions,
	mutations
}