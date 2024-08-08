/*TP3 */
import React, { useState, useEffect } from 'react';
const EditItem = ({ itemId }) => {
  const [formData, setFormData] = useState({
    nombre: '',
    precio: '',
    tipo: '',
    tipo_imagen: '',
    imagen: null,
  });
  const [mensaje, setMensaje] = useState('');

  useEffect(() => {
    const obtenerDetallesItem = async () => {
      try {
        const apiUrl = `http://localhost:8000/api/item/obtenerporid/${itemId}`;
        const response = await fetch(apiUrl);

        if (response.ok) {
          const data = await response.json();
          console.log('Detalles del ítem:', data);
          setFormData({
            nombre: data.nombre,
            precio: data.precio,
            tipo: data.tipo,
            tipo_imagen: data.tipo_imagen,
            imagen: null,
          });
        } else {
          setMensaje('Error al obtener detalles del ítem.');
          console.error('Error al obtener detalles del ítem:', response.statusText);
        }
      } catch (error) {
        console.error('Error al obtener detalles del ítem:', error);
        setMensaje('Error al obtener detalles del ítem.');
      }
    };

    obtenerDetallesItem();
  }, [itemId]);

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
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    const apiUrl = `http://localhost:8000/api/items/modificar/${itemId}`;

    const formDataApi = {
      nombre: formData.nombre,
      precio: formData.precio,
      tipo: formData.tipo,
      tipo_imagen: formData.tipo_imagen,
      imagen: formData.imagen,
    };

    try {
      const response = await fetch(apiUrl, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(formDataApi),
      });

      console.log('Respuesta PUT:', response);

      if (response.ok) {
        const data = await response.json();
        setMensaje(data.message);
      } else {
        setMensaje('Error al editar el ítem.');
        console.error('Error al editar el ítem:', response.statusText);
      }
    } catch (error) {
      console.error('Error en la solicitud PUT:', error);
    }
  };


  return (
    <div className="EditItem">
      <h1>Editar Item</h1>
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
          <select name="tipo" value={formData.tipo} onChange={handleInputChange}>
            <option value="comida">Comida</option>
            <option value="bebida">Bebida</option>
          </select>
        </div>

        <div>
          <label>Tipo Imagen:</label>
          <input type="text" name="tipo_imagen" value={formData.tipo_imagen} onChange={handleInputChange} />
        </div>

        <div>
          <label>Imagen:</label>
          <input type="file" name="imagen" accept="image/*" onChange={handleImageChange} />
        </div>

        <button type="submit">Guardar Cambios</button>
      </form>

      {mensaje && <p>{mensaje}</p>}
    </div>
  );
};

export default EditItem;
