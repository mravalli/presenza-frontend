import jwt_decode from "jwt-decode";
import store from '../store'

//let baseURL = process.env.VUE_APP_API_URL

const tokenUtils = {
    decodeToken: function(token) {
        return jwt_decode(token);
    },
    getResetToken: function() {
        return localStorage.getItem('refresh');
    },
    saveRefreshToken: function(newToken) {
        store.commit('addWebToken', newToken);
    },
}

export default tokenUtils;