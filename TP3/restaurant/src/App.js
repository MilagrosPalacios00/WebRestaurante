import React from "react";
import HeaderComponent from "./components/HeaderComponent";
import FooterComponent from "./components/FooterComponent";
import NavBarComponent from "./components/NavBarComponent";


function App() {
  return (
    <div>
      <HeaderComponent />
      <NavBarComponent /> 
      <FooterComponent/>
  </div>
  );
}

export default App;