import { generatePromiseActionType } from '../actionTypes';
import { ADD_MATERIAL, GET_PLATFORM_AGENT, USE_PLATFORM_AGENT } from './commonActions';

// Internal action types (only used within this slice)
const FETCH_MATERIALS = generatePromiseActionType('FETCH_MATERIALS');
const UPDATE_MATERIAL = generatePromiseActionType('UPDATE_MATERIAL');
const DELETE_MATERIAL = generatePromiseActionType('DELETE_MATERIAL');
const BULK_DELETE_MATERIAL = generatePromiseActionType('BULK_DELETE_MATERIAL');

// Initial state for materials domain
export const materialInitialState = {
  materials: [],
  materialsLoading: false
};

// Material slice reducer
export const materialReducer = (state, action) => {
  switch (action.type) {
    case FETCH_MATERIALS.REQUEST:
      return { ...state, materialsLoading: true };

    case FETCH_MATERIALS.SUCCESS:
      if (action.payload.result?.length === 0) {
        return { ...state, materialsLoading: false };
      }
      return {
        ...state,
        materials: action.payload.result,
        materialsLoading: false
      };

    case FETCH_MATERIALS.ERROR:
      return { ...state, materialsLoading: false };

    case ADD_MATERIAL.REQUEST:
    case ADD_MATERIAL.ERROR:
      return state;

    case ADD_MATERIAL.SUCCESS:
      return {
        ...state,
        materials: [...state.materials, { ...action.payload.result, status: 'PROCESSED' }]
      };

    case UPDATE_MATERIAL.REQUEST:
    case UPDATE_MATERIAL.ERROR:
      return state;

    case UPDATE_MATERIAL.SUCCESS:
      const updatedMaterial = action.payload.result;
      const updatedMaterials = state.materials.map(material => {
        if (material.uuid === updatedMaterial.uuid) {
          return { ...material, ...updatedMaterial };
        }
        return material;
      });
      return { ...state, materials: updatedMaterials };

    case DELETE_MATERIAL.REQUEST:
    case DELETE_MATERIAL.ERROR:
      return state;

    case DELETE_MATERIAL.SUCCESS:
      if (!action.payload.result) return state;
      const withoutDeletedMaterials = state.materials.filter(
        material => material.uuid !== action.payload.materialId
      );
      return { ...state, materials: withoutDeletedMaterials };

    case BULK_DELETE_MATERIAL.REQUEST:
    case BULK_DELETE_MATERIAL.ERROR:
      return state;

    case BULK_DELETE_MATERIAL.SUCCESS:
      if (!action.payload.result) return state;
      const withoutBulkDeletedMaterials = state.materials.filter(
        material => !action.payload.materialIds.includes(material.uuid)
      );
      return { ...state, materials: withoutBulkDeletedMaterials };

    case USE_PLATFORM_AGENT.SUCCESS:
    case GET_PLATFORM_AGENT.SUCCESS:
      const {
        result: { content = undefined, agentMaterials = [] }
      } = action.payload;

      if (content === false) {
        return state;
      }

      return { ...state, materials: agentMaterials };

    default:
      return state;
  }
};

// Material action creators
export const materialActionCreators = {
  fetchMaterialsRequest: () => ({
    type: FETCH_MATERIALS.REQUEST
  }),

  fetchMaterialsSuccess: result => ({
    type: FETCH_MATERIALS.SUCCESS,
    payload: { result }
  }),

  fetchMaterialsError: () => ({
    type: FETCH_MATERIALS.ERROR
  }),

  addMaterialRequest: () => ({
    type: ADD_MATERIAL.REQUEST
  }),

  addMaterialSuccess: result => ({
    type: ADD_MATERIAL.SUCCESS,
    payload: { result }
  }),

  addMaterialError: () => ({
    type: ADD_MATERIAL.ERROR
  }),

  updateMaterialRequest: () => ({
    type: UPDATE_MATERIAL.REQUEST
  }),

  updateMaterialSuccess: result => ({
    type: UPDATE_MATERIAL.SUCCESS,
    payload: { result }
  }),

  updateMaterialError: () => ({
    type: UPDATE_MATERIAL.ERROR
  }),

  deleteMaterialRequest: () => ({
    type: DELETE_MATERIAL.REQUEST
  }),

  deleteMaterialSuccess: (result, materialId) => ({
    type: DELETE_MATERIAL.SUCCESS,
    payload: { result, materialId }
  }),

  deleteMaterialError: () => ({
    type: DELETE_MATERIAL.ERROR
  }),

  bulkDeleteMaterialRequest: () => ({
    type: BULK_DELETE_MATERIAL.REQUEST
  }),

  bulkDeleteMaterialSuccess: (result, materialIds) => ({
    type: BULK_DELETE_MATERIAL.SUCCESS,
    payload: { result, materialIds }
  }),

  bulkDeleteMaterialError: () => ({
    type: BULK_DELETE_MATERIAL.ERROR
  })
};
