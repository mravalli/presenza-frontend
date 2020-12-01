import jwt_decode from "jwt-decode";

//let baseURL = process.env.VUE_APP_API_URL

const tokenUtils = {
    decodeToken: function(token) {
        return jwt_decode(token);
    },
    getResetToken: function() {},
    saveRefreshToken: function(newToken) {
        console.log(newToken)
    },
}

export default tokenUtils;