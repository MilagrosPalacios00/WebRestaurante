// HeaderComponent.jsx

import React from 'react';
import logo from '../logo.png';  // Importar la imagen usando require

const HeaderComponent = () => {
  return (
    <header>
      <img src={logo} alt="logo" /> {/* Utilizar la variable importada como src */}
    </header>
  );
};

export default HeaderComponent;

