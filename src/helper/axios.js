import axios from 'axios'
import store from '../store'
import router from '../router'
import tokenUtils from './tokenUtils'

let baseURL = import.meta.env.VITE_API_URL

// https://www.techynovice.com/setting-up-JWT-token-refresh-mechanism-with-axios/
let isAlreadyFetchingAccessToken = false;
// This is the list of waiting requests that will retry after the JWT refresh complete
let subscribers = [];

async function resetTokenAndReattemptRequest(error) {
    try {
        const { response: errorResponse } = error;
        const resetToken = await tokenUtils.getResetToken();
        if (!resetToken) {
            // We can't refresh, throw the error anyway
            return Promise.reject(error);
        }
        /* Proceed to the token refresh procedure
        We create a new Promise that will retry the request,
        clone all the request configuration from the failed
        request in the error object. */
        const retryOriginalRequest = new Promise(resolve => {
            /* We need to add the request retry to the queue
            since there another request that already attempt to
            refresh the token */
            addSubscriber(access_token => {
                errorResponse.config.headers.Authorization = 'Bearer ' + access_token;
                resolve(axios(errorResponse.config));
            });
        });
        if (!isAlreadyFetchingAccessToken) {
            isAlreadyFetchingAccessToken = true;
            const response = await axios({
                method: 'post',
                url: `${baseURL}/auth/refresh`,
                data: {
                    token: resetToken // Just an example, your case may vary
                }
            });
            if (!response.data) {
                return Promise.reject(error);
            }
            const newToken = response.data.token.access_token;
            tokenUtils.saveRefreshToken(newToken); // save the newly refreshed token for other requests to use
            isAlreadyFetchingAccessToken = false;
            onAccessTokenFetched(newToken);
        }
        return retryOriginalRequest;
    } catch (err) {
        tokenUtils.removeWebToken();
        router.push('/login');
    }
}

function onAccessTokenFetched(access_token) {
    // When the refresh is successful, we start retrying the requests one by one and empty the queue
    subscribers.forEach(callback => callback(access_token));
    subscribers = [];
}

function addSubscriber(callback) {
    subscribers.push(callback);
}

function isTokenExpiredError(errorResponse) {
    if (errorResponse && errorResponse.status === 403) {
        return true;
    }
    return false;
}

const axiosApiInstance = axios.create({
    baseURL: baseURL
});

axiosApiInstance.interceptors.request.use(
    config => {
        const token = store.state.user.access_token;
        config.headers.Authorization =  token ? `Bearer ${token}` : '';
        return config;
    },
    error => {
        Promise.reject(error);
    }
);

axiosApiInstance.interceptors.response.use(
    (response) => {
        return response;
    },
    async function (error) {
        const errorResponse = error.response
        if (isTokenExpiredError(errorResponse)) {
            return resetTokenAndReattemptRequest(error)
        }
        return Promise.reject(error)
    }
);

export default axiosApiInstance