<!-- Modal -->
<div id="modal" class="modal">
    <div class="modal__content">
        <div class="p-5" id="modal-content">
            <h2 class="text-lg font-medium">Agregar Nuevo Horario</h2>
            <form id="addScheduleForm" method="POST" action="">
                <div class="mt-4">
                    <label for="modal-sede" class="form-label">Sede:</label>
                    <input type="text" id="modal-sede" name="modal-sede" class="input w-full border mt-2" required>
                </div>
                <div class="mt-4">
                    <label for="modal-dia" class="form-label">Día:</label>
                    <select name="modal-dia" id="modal-dia" class="input w-full border mt-2">
                        <option value="Lunes">Lunes</option>
                        <option value="Martes">Martes</option>
                        <option value="Miércoles">Miércoles</option>
                        <option value="Jueves">Jueves</option>
                        <option value="Viernes">Viernes</option>
                        <option value="Sábado">Sábado</option>
                    </select>
                </div>
                <div class="mt-4">
                    <button type="submit" class="button bg-theme-1 text-white">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
