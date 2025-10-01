import "./bootstrap";
import $ from "jquery";
import "slicknav";

$(function () {
    $(".header__menu ul").slicknav({
        prependTo: "#mobile-menu-wrap",
        label: "", // biar cuma icon ☰ tanpa tulisan "MENU"
        allowParentLinks: true,
    });
});
