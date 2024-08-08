import React, { useState } from 'react';

function NewItem() {
  const [formData, setFormData] = useState({
    nombre: '',
    precio: '',
    tipo: 'comida',
    tipo_imagen : '',
    imagen: null,
  });
  const [mensaje, setMensaje] = useState('');
  const [mostrarFormulario, setMostrarFormulario] = useState(false);

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData({
      ...formData,
      [name]: value,
    });
  };

  const handleImageChange = (e) => {
    const file = e.target.files[0];
    setFormData({
      ...formData,
      imagen: file,
    });

    // Convertir imagen a base64
    if (file) {
    var reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onloadend = function() {
        var base64 = reader.result.split(',');
        console.log(base64);
        setFormData({
            ...formData,
            imagen: file,
            imagenBase64: base64[1], // Guardar solo la parte de datos base64
          });
      };
      
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    if (!formData.nombre.trim()) {
      setMensaje('Ingrese un nombre válido.');
      return;
    }

    if (!formData.precio.trim() || isNaN(Number(formData.precio))) {
      setMensaje('Ingrese un precio válido.');
      return;
    }

    const apiUrl = 'http://localhost:8000/api/items/nuevo';
    const formDataApi = new FormData();
    formDataApi.append('nombre', formData.nombre);
    formDataApi.append('precio', formData.precio);
    formDataApi.append('tipo', formData.tipo);
    formDataApi.append('tipo_imagen', formData.tipo_imagen);
    formDataApi.append('imagen', formData.imagenBase64);

    try {
      console.log(formDataApi)
      const response = await fetch(apiUrl, {
        method: 'POST',
        body: formDataApi,
      });

      if (response.ok) {
        const data = await response.json();
        setMensaje(data.message);
        setFormData({
          nombre: '',
          precio: '',
          tipo: 'comida',
          tipo_imagen : '',
          imagen: null,
          imagenBase64: null,
        });
        setMostrarFormulario(false);
      } else {
        setMensaje('Error al enviar el formulario.');
      }
    } catch (error) {
      console.error('Error en la solicitud POST:', error);
    }
  };

  return (
    <div className="NewItem">
      <h1>Nuevo Item</h1>
      {!mostrarFormulario ? (
        <button onClick={() => setMostrarFormulario(true)}>Nuevo Item</button>
      ) : (
        <form onSubmit={handleSubmit}>
          <div>
            <label>Nombre:</label>
            <input type="text" name="nombre" value={formData.nombre} onChange={handleInputChange} />
          </div>

          <div>
            <label>Precio:</label>
            <input type="text" name="precio" value={formData.precio} onChange={handleInputChange} />
          </div>

          <div>
            <label>Tipo:</label>
            <select name="tipo" defaultValue={formData.tipo} onChange={handleInputChange}>
              <option value="comida">Comida</option>
              <option value="bebida">Bebida</option>
            </select>
          </div>

          <div>
            <label>tipo_imagen:</label>
            <input type="text" name="tipo_imagen" value={formData.tipo_imagen} onChange={handleInputChange} />
          </div>

          <div>
            <label>Imagen:</label>
            <input type="file" name="imagen" accept="image/*" onChange={handleImageChange} />
          </div>
          
          <button type="submit">Crear Nuevo Item</button>
          <button type="button" onClick={() => setMostrarFormulario(false)}>
            Cancelar
          </button>
        </form>
      )}

      {mensaje && <p>{mensaje}</p>}
    </div>
  );
}

export default NewItem;
