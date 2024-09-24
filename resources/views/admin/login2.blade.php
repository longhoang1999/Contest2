<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="{{ asset('pagecss/login.css') }}">
</head>

<body ng-app="myApp">
    <div class="container" ng-controller="myController">

        {{-- <div class="box signin">
            <h2>Bạn đã có tài khoản?</h2>
            <button ng-click="signinBtn()">Đăng nhập</button>
        </div>
        <div  class="box signup">
            <h2>Bạn không có tài khoản?</h2>
            <button ng-click="signupBtn()">Đăng ký</button>
        </div> --}}
        <div class="formBx" style="left: auto;">
            <div class="form signInForm">
                <p ng-show="showMesTus" ng-bind="mesTus" class="showMesTus"></p>
                <form class="loginForm" method="post" action="{!! route('admin.postLogin') !!}">
                    @csrf
                    <h3>Đăng nhập</h3>
                    <input type="text" placeholder="Tên đăng nhập" name="tendn" ng-model="data2.tendn">
                    <span ng-show="validate2.tendn" class="text-danger">Không được để trống tên đăng nhập</span>
                    <input type="password" placeholder="Password" name="password" ng-model="data2.password">
                    <span ng-show="validate2.password" class="text-danger">Không được để trống password</span>
                    <input type="button" value="Đăng nhập" ng-click="submit2()">
                    {{-- <a href="#" class="forgot">
                        Quên mật khẩu
                    </a> --}}
                </form>
            </div>
            <div class="form signUpForm">
                <form class="registerForm" method="post" action="{!! route('admin.register') !!}"   >
                    @csrf
                    <h3>Đăng ký</h3>
                    <input type="text" placeholder="Họ và tên" name="name" ng-model="data.name">
                    <span ng-show="validate.name" class="text-danger">Không được để trống name</span>
                    <input type="text" placeholder="Email" name="email" ng-model="data.email" >
                    <span ng-show="validate.email" class="text-danger">Không được để trống email</span>
                    <span ng-show="validate.systaxEmail" class="text-danger">Không đúng định dạng email</span>
                    <input type="text" placeholder="Tên đăng nhập" name="tendn" ng-model="data.tendn" >
                    <span ng-show="validate.tendn" class="text-danger">Không được để trống tên đăng nhập</span>
                    <input type="password" placeholder="Mật khẩu" name="password" ng-model="data.password" >
                    <span ng-show="validate.password" class="text-danger">Không được để trống password</span>
                    <span ng-show="validate.equal" class="text-danger">Password và Confirm không khớp</span>
                    <input type="password" placeholder="Nhập lại mật khẩu" ng-model="data.confirm">
                    <span ng-show="validate.confirm" class="text-danger">Không được để trống confirm</span>
                    <input type="button" ng-click="submit()" value="Đăng ký">
                </form>
            </div>

        </div>
    </div>

     <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular.min.js"></script>
     <script>
            var app = angular.module("myApp", [])
            app.controller("myController", function($scope) {
                var body = angular.element( document.querySelector( 'body' ) );
                $scope.signupBtn = function() {
                    body.addClass('slide');
                }
                $scope.signinBtn = function(){
                    body.removeClass('slide');
                }
                $scope.data = {
                    name: "",
                    email: "",
                    password: "",
                    confirm: "",
                    tendn: ""
                }
                $scope.validate = {
                    name: false,
                    email: false,
                    tendn: false,
                    password: false,
                    confirm: false,
                    equal: false,
                    systaxEmail: false
                }
                $scope.submit = function(){
                    if(checkEmpty()){
                        if(checkConfirm()){
                            if(checkEmail()){
                                var registerForm = angular.element( document.querySelector( '.registerForm' ) );
                                registerForm[0].submit()
                            }
                        }
                    }
                }
                function checkEmpty() {
                    $scope.validate.name = $scope.data.name == "" ? true : false
                    $scope.validate.email = $scope.data.email == "" ? true : false
                    $scope.validate.password = $scope.data.password == "" ? true : false
                    $scope.validate.confirm = $scope.data.confirm == "" ? true : false
                    $scope.validate.tendn = $scope.data.tendn == "" ? true : false

                    if(!$scope.validate.name && !$scope.validate.email && !$scope.validate.password && !$scope.validate.confirm && !$scope.validate.tendn){
                        return true
                    }else{
                        return false
                    }
                }
                function checkConfirm(){
                    if($scope.data.password === $scope.data.confirm){
                        $scope.validate.equal = false;
                        return true
                    }else{
                        $scope.validate.equal = true;
                        return false
                    }
                }
                function checkEmail(){
                    if($scope.data.email.includes("@") && $scope.data.email.includes(".")){
                        $scope.validate.systaxEmail = false
                        return true
                    }else{
                        $scope.validate.systaxEmail = true
                        return false
                    }
                }
                @if(\Session::has('error'))
                    $scope.showMesTus = true
                    $scope.mesTus = "{!! \Session::get('error') !!}"
                    setTimeout(() => {
                        $scope.showMesTus = false
                    }, 4000);
                @elseif(\Session::has('success'))
                    $scope.showMesTus = true
                    $scope.mesTus = "{!! \Session::get('success') !!}"
                    setTimeout(() => {
                        $scope.showMesTus = false
                    }, 4000);
                @endif




                /// Login
                $scope.validate2 = {
                    // email: false,
                    // systaxEmail: false,
                    tendn: false,
                    password: false
                }
                $scope.data2 = {
                    // email: "",
                    password: "",
                    tendn: ""
                }
                function checkEmpty2(){
                    $scope.validate2.tendn = $scope.data2.tendn == "" ? true : false
                    $scope.validate2.password = $scope.data2.password == "" ? true : false
                    if(!$scope.validate2.tendn && !$scope.validate2.password){
                        return true
                    }else{
                        return false
                    }
                }
                // function checkEmail2(){
                //     if($scope.data2.email.includes("@") && $scope.data2.email.includes(".")){
                //         $scope.validate2.systaxEmail = false
                //         return true
                //     }else{
                //         $scope.validate2.systaxEmail = true
                //         return false
                //     }
                // }
                $scope.submit2 = function(){
                    if(checkEmpty2()){
                        var loginForm = angular.element( document.querySelector( '.loginForm' ) );
                        loginForm[0].submit()
                    }
                }
            })
     </script>
</body>

</html>
