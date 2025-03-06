import axios from 'axios';
import './bootstrap';


const nickname=document.getElementById('nickname');
const message=document.getElementById('message');


const submitButton=document.getElementById('submitButton');

submitButton.addEventListener('click',(){

    axios.post('/chat',{
        nickname : nickname,
        message: message
    });

});
